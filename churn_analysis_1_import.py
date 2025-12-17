# ============================================
# Customer Churn Analysis - Part 1: Data Import and Preprocessing
# ============================================
# This script imports customer churn data from MySQL database or CSV file
# and performs initial data analysis and visualization
# ============================================

# Import necessary libraries
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
from sklearn.preprocessing import StandardScaler
import warnings
warnings.filterwarnings('ignore')

# Set visualization style
sns.set(style="whitegrid")
plt.rcParams['figure.figsize'] = (10, 6)
plt.rcParams['font.sans-serif'] = ['SimHei']  # Support Chinese characters
plt.rcParams['axes.unicode_minus'] = False  # Fix minus sign display

print("=" * 60)
print("Customer Churn Analysis - Part 1: Data Import")
print("=" * 60)

# Import data from MySQL database
def import_from_mysql():
    """Import customer churn data from MySQL database"""
    try:
        import pymysql

        # Database configuration (from config.php)
        db_config = {
            'host': '182.61.1.142',
            'port': 13306,
            'user': 'root',
            'password': 'BtXELjPMb4dadjPy',
            'database': 'ghb7zzwh6fy5j3yl',
            'charset': 'utf8mb4'
        }

        print("\nConnecting to MySQL database...")
        connection = pymysql.connect(**db_config)

        # Query customer churn data
        query = """
        SELECT
            email AS customer_email,
            months_as_customer,
            order_count,
            days_since_last_order,
            churned
        FROM customers
        WHERE months_as_customer IS NOT NULL
        ORDER BY id
        """

        df = pd.read_sql(query, connection)
        connection.close()

        print(f"✓ Successfully imported {len(df)} records from MySQL database")
        return df, 'mysql'

    except ImportError:
        print("✗ pymysql not installed. Install with: pip install pymysql")
        return None, None
    except Exception as e:
        print(f"✗ Database connection failed: {e}")
        return None, None

# Import data from CSV file
def import_from_csv():
    """Import customer churn data from CSV file"""
    try:
        df = pd.read_csv('churn_data.csv')
        print(f"✓ Successfully imported {len(df)} records from churn_data.csv")
        return df, 'csv'
    except FileNotFoundError:
        print("✗ churn_data.csv not found")
        return None, None
    except Exception as e:
        print(f"✗ Error reading CSV file: {e}")
        return None, None

# Generate synthetic data
def generate_synthetic_data():
    """Generate synthetic customer churn data"""
    print("\nGenerating synthetic data...")

    np.random.seed(42)
    n_customers = 100

    customer_emails = [f"customer{i}@example.com" for i in range(1, n_customers + 1)]
    months_as_customer = np.random.randint(1, 36, n_customers)
    order_count = np.random.randint(0, 10, n_customers)

    days_since_last_order = []
    for i in range(n_customers):
        if months_as_customer[i] > 12 and order_count[i] > 3:
            days = np.random.randint(1, 60)
        elif months_as_customer[i] > 6 or order_count[i] > 1:
            days = np.random.randint(30, 120)
        else:
            days = np.random.randint(60, 180)
        days_since_last_order.append(days)

    churned = [1 if days > 90 else 0 for days in days_since_last_order]

    df = pd.DataFrame({
        'customer_email': customer_emails,
        'months_as_customer': months_as_customer,
        'order_count': order_count,
        'days_since_last_order': days_since_last_order,
        'churned': churned
    })

    df.to_csv('churn_data.csv', index=False)
    print(f"✓ Generated {len(df)} synthetic records and saved to churn_data.csv")
    return df, 'synthetic'

# Main import logic
print("\nAttempting to import data...")
print("-" * 60)

# Try MySQL first
df, data_source = import_from_mysql()

# If MySQL fails, try CSV
if df is None:
    print("\nTrying CSV file...")
    df, data_source = import_from_csv()

# If CSV fails, generate synthetic data
if df is None:
    print("\nGenerating synthetic data as fallback...")
    df, data_source = generate_synthetic_data()

# Display import results
print("\n" + "=" * 60)
print(f"Data Source: {data_source.upper()}")
print(f"Dataset Shape: {df.shape[0]} rows × {df.shape[1]} columns")
print("=" * 60)

# Display first few rows
print("\nFirst 5 rows of data:")
print(df.head())

# Data preprocessing
print("\n" + "=" * 60)
print("Data Preprocessing")
print("=" * 60)

# Check for missing values
print("\nMissing value check:")
missing_counts = df.isnull().sum()
print(missing_counts)

if missing_counts.sum() > 0:
    print("\n⚠ Missing values detected. Filling with appropriate values...")
    # Fill missing values with 0 for days_since_last_order (customers with no orders)
    df['days_since_last_order'].fillna(0, inplace=True)
    # Fill other missing values with median
    df['months_as_customer'].fillna(df['months_as_customer'].median(), inplace=True)
    df['order_count'].fillna(0, inplace=True)
    print("✓ Missing values have been filled")
else:
    print("✓ No missing values found")

# Ensure all numeric columns are actually numeric and replace any remaining NaN with 0
print("\nConverting to numeric types...")
df['months_as_customer'] = pd.to_numeric(df['months_as_customer'], errors='coerce').fillna(0).astype(int)
df['order_count'] = pd.to_numeric(df['order_count'], errors='coerce').fillna(0).astype(int)
df['days_since_last_order'] = pd.to_numeric(df['days_since_last_order'], errors='coerce').fillna(0).astype(int)
df['churned'] = pd.to_numeric(df['churned'], errors='coerce').fillna(0).astype(int)
print("✓ All columns converted to numeric")

# Check data types
print("\nData types:")
print(df.dtypes)

# Basic statistics
print("\nBasic statistics:")
print(df.describe())

# Target variable distribution
print("\n" + "=" * 60)
print("Churn Analysis")
print("=" * 60)

churn_counts = df['churned'].value_counts()
churn_rate = df['churned'].mean()

print("\nChurn distribution:")
print(f"Active customers (0): {churn_counts.get(0, 0)}")
print(f"Churned customers (1): {churn_counts.get(1, 0)}")
print(f"Churn rate: {churn_rate:.2%}")

# Visualizations
print("\nGenerating visualizations...")

# 1. Churn Distribution
plt.figure(figsize=(8, 5))
colors = ['#2ecc71', '#e74c3c']
bars = plt.bar(['Active', 'Churned'],
               [churn_counts.get(0, 0), churn_counts.get(1, 0)],
               color=colors,
               alpha=0.8,
               edgecolor='black',
               linewidth=1.2)

plt.title('Customer Churn Distribution', fontsize=16, fontweight='bold', pad=20)
plt.xlabel('Churn Status', fontsize=12)
plt.ylabel('Number of Customers', fontsize=12)
plt.grid(axis='y', alpha=0.3)

# Add value labels on bars
for bar in bars:
    height = bar.get_height()
    plt.text(bar.get_x() + bar.get_width()/2., height,
             f'{int(height)}',
             ha='center', va='bottom', fontsize=12, fontweight='bold')

plt.tight_layout()
plt.savefig('churn_distribution.png', dpi=300, bbox_inches='tight')
print("✓ Saved: churn_distribution.png")
plt.show()

# 2. Feature distributions by churn status
fig, axes = plt.subplots(1, 3, figsize=(15, 4))

features = ['months_as_customer', 'order_count', 'days_since_last_order']
titles = ['Months as Customer', 'Order Count', 'Days Since Last Order']

for idx, (feature, title) in enumerate(zip(features, titles)):
    axes[idx].hist([df[df['churned']==0][feature],
                    df[df['churned']==1][feature]],
                   bins=15,
                   label=['Active', 'Churned'],
                   color=['#2ecc71', '#e74c3c'],
                   alpha=0.7,
                   edgecolor='black')
    axes[idx].set_title(title, fontsize=12, fontweight='bold')
    axes[idx].set_xlabel(title, fontsize=10)
    axes[idx].set_ylabel('Frequency', fontsize=10)
    axes[idx].legend()
    axes[idx].grid(axis='y', alpha=0.3)

plt.tight_layout()
plt.savefig('feature_distributions.png', dpi=300, bbox_inches='tight')
print("✓ Saved: feature_distributions.png")
plt.show()

# 3. Correlation heatmap
plt.figure(figsize=(8, 6))
numeric_cols = ['months_as_customer', 'order_count', 'days_since_last_order', 'churned']
correlation_matrix = df[numeric_cols].corr()

sns.heatmap(correlation_matrix,
            annot=True,
            fmt='.2f',
            cmap='RdYlGn_r',
            center=0,
            square=True,
            linewidths=1,
            cbar_kws={"shrink": 0.8})

plt.title('Feature Correlation Heatmap', fontsize=14, fontweight='bold', pad=20)
plt.tight_layout()
plt.savefig('correlation_heatmap.png', dpi=300, bbox_inches='tight')
print("✓ Saved: correlation_heatmap.png")
plt.show()

# Save processed data for Part 2
df.to_csv('churn_data_processed.csv', index=False)
print("\n✓ Saved processed data to: churn_data_processed.csv")

# Summary
print("\n" + "=" * 60)
print("Summary")
print("=" * 60)
print(f"Total Customers: {len(df)}")
print(f"Active Customers: {churn_counts.get(0, 0)} ({(1-churn_rate)*100:.1f}%)")
print(f"Churned Customers: {churn_counts.get(1, 0)} ({churn_rate*100:.1f}%)")
print(f"\nKey Findings:")
print(f"- Average months as customer: {df['months_as_customer'].mean():.1f}")
print(f"- Average order count: {df['order_count'].mean():.1f}")
print(f"- Average days since last order: {df['days_since_last_order'].mean():.1f}")
print("\n✓ Part 1 Complete! Ready for Part 2 (Model Training)")
print("=" * 60)
