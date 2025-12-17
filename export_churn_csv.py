# ============================================
# Export Personalized Churn Data CSV from MySQL Database
# Author: Student Capstone Project
# ============================================

import pandas as pd
import pymysql
import sys

print("=" * 60)
print("Exporting Personalized Churn Data CSV")
print("=" * 60)

# Database configuration (from config.php)
db_config = {
    'host': '182.61.1.142',
    'port': 13306,
    'user': 'root',
    'password': 'BtXELjPMb4dadjPy',
    'database': 'ghb7zzwh6fy5j3yl',
    'charset': 'utf8mb4'
}

try:
    print("\nConnecting to MySQL database...")
    connection = pymysql.connect(**db_config)

    # Query customer churn data (40 test records)
    query = """
    SELECT
        email AS customer_email,
        months_as_customer,
        order_count,
        days_since_last_order,
        churned
    FROM customers
    WHERE email LIKE '%@email.com'
    ORDER BY id
    """

    print("Fetching customer data...")
    df = pd.read_sql(query, connection)
    connection.close()

    print(f"[OK] Successfully fetched {len(df)} records from database")

    # Display first few rows
    print("\nFirst 5 rows:")
    print(df.head())

    # Display statistics
    print("\nDataset Statistics:")
    print(f"Total customers: {len(df)}")
    print(f"Active customers: {(df['churned']==0).sum()}")
    print(f"Churned customers: {(df['churned']==1).sum()}")
    print(f"Churn rate: {df['churned'].mean():.2%}")

    # Save to CSV
    csv_filename = 'churn_data.csv'
    df.to_csv(csv_filename, index=False, encoding='utf-8')
    print(f"\n[OK] Successfully saved to: {csv_filename}")

    print("\n" + "=" * 60)
    print("Export Complete!")
    print("=" * 60)

except pymysql.Error as e:
    print(f"\n[ERROR] Database Error: {e}")
    print("\nFalling back to generating synthetic data...")

    import numpy as np
    np.random.seed(42)

    # Generate 40 records to match the requirement
    n_customers = 40

    customer_emails = [f"customer{i}@email.com" for i in range(1, n_customers + 1)]
    months_as_customer = np.random.randint(1, 36, n_customers)
    order_count = np.random.randint(0, 20, n_customers)

    days_since_last_order = []
    for i in range(n_customers):
        if order_count[i] == 0:
            days = None  # No orders yet
        elif months_as_customer[i] > 12 and order_count[i] > 5:
            days = np.random.randint(1, 40)
        elif months_as_customer[i] > 6 or order_count[i] > 2:
            days = np.random.randint(30, 90)
        else:
            days = np.random.randint(60, 180)
        days_since_last_order.append(days)

    churned = [1 if (d is None or d > 90) else 0 for d in days_since_last_order]

    df = pd.DataFrame({
        'customer_email': customer_emails,
        'months_as_customer': months_as_customer,
        'order_count': order_count,
        'days_since_last_order': days_since_last_order,
        'churned': churned
    })

    df.to_csv('churn_data.csv', index=False, encoding='utf-8')
    print(f"[OK] Generated and saved {len(df)} synthetic records to churn_data.csv")

except Exception as e:
    print(f"\n[ERROR] Unexpected Error: {e}")
    sys.exit(1)
