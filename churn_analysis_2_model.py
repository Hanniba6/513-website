# ============================================
# Customer Churn Analysis - Part 2: Model Training and Prediction
# ============================================
# This script trains a logistic regression model to predict customer churn
# and evaluates its performance
# ============================================

# Import necessary libraries
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report, roc_curve, roc_auc_score
from sklearn.preprocessing import StandardScaler
import warnings
warnings.filterwarnings('ignore')

# Set visualization style
sns.set(style="whitegrid")
plt.rcParams['figure.figsize'] = (10, 6)
plt.rcParams['font.sans-serif'] = ['SimHei']  # Support Chinese characters
plt.rcParams['axes.unicode_minus'] = False  # Fix minus sign display

print("=" * 60)
print("Customer Churn Analysis - Part 2: Model Training")
print("=" * 60)

# Load processed data from Part 1
try:
    df = pd.read_csv('churn_data_processed.csv')
    print(f"\n✓ Loaded {len(df)} records from churn_data_processed.csv")
except FileNotFoundError:
    print("\n✗ Error: churn_data_processed.csv not found")
    print("Please run churn_analysis_1_import.py first!")
    exit(1)

# Prepare features and target
print("\n" + "=" * 60)
print("Preparing Data for Machine Learning")
print("=" * 60)

# Select features for the model
feature_columns = ['months_as_customer', 'order_count', 'days_since_last_order']
X = df[feature_columns]
y = df['churned']

print(f"\nFeatures: {feature_columns}")
print(f"Target: churned")
print(f"\nFeature matrix shape: {X.shape}")
print(f"Target vector shape: {y.shape}")

# Split data into training and testing sets
print("\nSplitting data into train/test sets (80/20 split)...")
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y
)

print(f"Training set: {X_train.shape[0]} samples")
print(f"Testing set: {X_test.shape[0]} samples")

# Feature scaling
print("\nApplying feature scaling (StandardScaler)...")
scaler = StandardScaler()
X_train_scaled = scaler.fit_transform(X_train)
X_test_scaled = scaler.transform(X_test)

print("✓ Features scaled successfully")

# Train the model
print("\n" + "=" * 60)
print("Training Logistic Regression Model")
print("=" * 60)

model = LogisticRegression(random_state=42, max_iter=1000)
model.fit(X_train_scaled, y_train)

print("✓ Model trained successfully")

# Display model coefficients
print("\nModel Coefficients:")
for feature, coef in zip(feature_columns, model.coef_[0]):
    direction = "↑ Higher" if coef > 0 else "↓ Lower"
    print(f"  {feature}: {coef:.4f} ({direction} values increase churn risk)")

print(f"\nIntercept: {model.intercept_[0]:.4f}")

# Make predictions
print("\n" + "=" * 60)
print("Making Predictions")
print("=" * 60)

y_train_pred = model.predict(X_train_scaled)
y_test_pred = model.predict(X_test_scaled)
y_test_proba = model.predict_proba(X_test_scaled)[:, 1]

print("✓ Predictions generated")

# Evaluate model performance
print("\n" + "=" * 60)
print("Model Performance")
print("=" * 60)

train_accuracy = accuracy_score(y_train, y_train_pred)
test_accuracy = accuracy_score(y_test, y_test_pred)
auc_score = roc_auc_score(y_test, y_test_proba)

print(f"\nTraining Accuracy: {train_accuracy:.2%}")
print(f"Testing Accuracy: {test_accuracy:.2%}")
print(f"AUC-ROC Score: {auc_score:.4f}")

# Classification report
print("\nDetailed Classification Report (Test Set):")
print(classification_report(y_test, y_test_pred,
                          target_names=['Active', 'Churned'],
                          digits=3))

# Confusion Matrix
cm = confusion_matrix(y_test, y_test_pred)
print("\nConfusion Matrix:")
print(cm)
print(f"\nTrue Negatives (Correctly predicted Active): {cm[0, 0]}")
print(f"False Positives (Incorrectly predicted Churned): {cm[0, 1]}")
print(f"False Negatives (Incorrectly predicted Active): {cm[1, 0]}")
print(f"True Positives (Correctly predicted Churned): {cm[1, 1]}")

# Visualizations
print("\n" + "=" * 60)
print("Generating Model Evaluation Visualizations")
print("=" * 60)

# 1. Confusion Matrix Heatmap
plt.figure(figsize=(8, 6))
sns.heatmap(cm,
            annot=True,
            fmt='d',
            cmap='Blues',
            xticklabels=['Active', 'Churned'],
            yticklabels=['Active', 'Churned'],
            cbar_kws={'label': 'Count'},
            linewidths=2,
            linecolor='black')

plt.title('Confusion Matrix', fontsize=16, fontweight='bold', pad=20)
plt.ylabel('Actual', fontsize=12, fontweight='bold')
plt.xlabel('Predicted', fontsize=12, fontweight='bold')
plt.tight_layout()
plt.savefig('confusion_matrix.png', dpi=300, bbox_inches='tight')
print("✓ Saved: confusion_matrix.png")
plt.show()

# 2. ROC Curve
fpr, tpr, thresholds = roc_curve(y_test, y_test_proba)

plt.figure(figsize=(8, 6))
plt.plot(fpr, tpr, color='#e74c3c', linewidth=2.5, label=f'ROC Curve (AUC = {auc_score:.3f})')
plt.plot([0, 1], [0, 1], color='gray', linestyle='--', linewidth=1.5, label='Random Classifier')

plt.xlabel('False Positive Rate', fontsize=12, fontweight='bold')
plt.ylabel('True Positive Rate', fontsize=12, fontweight='bold')
plt.title('ROC Curve - Churn Prediction Model', fontsize=16, fontweight='bold', pad=20)
plt.legend(loc='lower right', fontsize=11)
plt.grid(alpha=0.3)
plt.tight_layout()
plt.savefig('roc_curve.png', dpi=300, bbox_inches='tight')
print("✓ Saved: roc_curve.png")
plt.show()

# 3. Feature Importance
feature_importance = pd.DataFrame({
    'Feature': feature_columns,
    'Coefficient': model.coef_[0],
    'Abs_Coefficient': np.abs(model.coef_[0])
}).sort_values('Abs_Coefficient', ascending=True)

plt.figure(figsize=(10, 6))
colors = ['#e74c3c' if x > 0 else '#2ecc71' for x in feature_importance['Coefficient']]
bars = plt.barh(feature_importance['Feature'],
                feature_importance['Coefficient'],
                color=colors,
                alpha=0.8,
                edgecolor='black',
                linewidth=1.2)

plt.xlabel('Coefficient Value', fontsize=12, fontweight='bold')
plt.ylabel('Features', fontsize=12, fontweight='bold')
plt.title('Feature Importance (Logistic Regression Coefficients)',
          fontsize=14,
          fontweight='bold',
          pad=20)
plt.axvline(x=0, color='black', linestyle='-', linewidth=0.8)
plt.grid(axis='x', alpha=0.3)

# Add legend
from matplotlib.patches import Patch
legend_elements = [
    Patch(facecolor='#e74c3c', label='Increases Churn Risk'),
    Patch(facecolor='#2ecc71', label='Decreases Churn Risk')
]
plt.legend(handles=legend_elements, loc='best', fontsize=10)

plt.tight_layout()
plt.savefig('feature_importance.png', dpi=300, bbox_inches='tight')
print("✓ Saved: feature_importance.png")
plt.show()

# 4. Prediction Probability Distribution
plt.figure(figsize=(10, 6))

# Separate probabilities by actual class
churned_proba = y_test_proba[y_test == 1]
active_proba = y_test_proba[y_test == 0]

plt.hist(active_proba, bins=20, alpha=0.7, color='#2ecc71',
         label='Actually Active', edgecolor='black')
plt.hist(churned_proba, bins=20, alpha=0.7, color='#e74c3c',
         label='Actually Churned', edgecolor='black')

plt.axvline(x=0.5, color='black', linestyle='--', linewidth=2,
            label='Decision Threshold (0.5)')

plt.xlabel('Predicted Churn Probability', fontsize=12, fontweight='bold')
plt.ylabel('Frequency', fontsize=12, fontweight='bold')
plt.title('Distribution of Predicted Churn Probabilities',
          fontsize=14,
          fontweight='bold',
          pad=20)
plt.legend(fontsize=11)
plt.grid(axis='y', alpha=0.3)
plt.tight_layout()
plt.savefig('probability_distribution.png', dpi=300, bbox_inches='tight')
print("✓ Saved: probability_distribution.png")
plt.show()

# Example predictions on new customers
print("\n" + "=" * 60)
print("Example Predictions on Sample Customers")
print("=" * 60)

sample_customers = pd.DataFrame({
    'months_as_customer': [6, 18, 30, 3],
    'order_count': [1, 8, 15, 0],
    'days_since_last_order': [120, 25, 10, 150]
})

sample_customers_scaled = scaler.transform(sample_customers)
sample_predictions = model.predict(sample_customers_scaled)
sample_probabilities = model.predict_proba(sample_customers_scaled)[:, 1]

print("\nSample Customer Predictions:")
print("-" * 60)
for i in range(len(sample_customers)):
    print(f"\nCustomer {i+1}:")
    print(f"  Months as Customer: {sample_customers.iloc[i]['months_as_customer']}")
    print(f"  Order Count: {sample_customers.iloc[i]['order_count']}")
    print(f"  Days Since Last Order: {sample_customers.iloc[i]['days_since_last_order']}")
    print(f"  Churn Probability: {sample_probabilities[i]:.2%}")
    print(f"  Prediction: {'CHURNED' if sample_predictions[i] == 1 else 'ACTIVE'}")
    print(f"  Risk Level: ", end="")
    if sample_probabilities[i] < 0.3:
        print("LOW 🟢")
    elif sample_probabilities[i] < 0.7:
        print("MEDIUM 🟡")
    else:
        print("HIGH 🔴")

# Save model results
results_summary = pd.DataFrame({
    'Metric': ['Training Accuracy', 'Testing Accuracy', 'AUC-ROC Score',
               'True Negatives', 'False Positives', 'False Negatives', 'True Positives'],
    'Value': [
        f"{train_accuracy:.4f}",
        f"{test_accuracy:.4f}",
        f"{auc_score:.4f}",
        cm[0, 0],
        cm[0, 1],
        cm[1, 0],
        cm[1, 1]
    ]
})

results_summary.to_csv('model_results.csv', index=False)
print("\n✓ Saved model results to: model_results.csv")

# Final summary
print("\n" + "=" * 60)
print("Analysis Complete!")
print("=" * 60)
print("\n📊 Generated Files:")
print("  1. churn_distribution.png - Customer churn distribution")
print("  2. feature_distributions.png - Feature distributions by churn status")
print("  3. correlation_heatmap.png - Feature correlation matrix")
print("  4. confusion_matrix.png - Model confusion matrix")
print("  5. roc_curve.png - ROC curve and AUC score")
print("  6. feature_importance.png - Feature importance coefficients")
print("  7. probability_distribution.png - Predicted probability distribution")
print("  8. model_results.csv - Model performance metrics")

print("\n🎯 Key Insights:")
print(f"  • Model achieves {test_accuracy:.1%} accuracy on test data")
print(f"  • AUC-ROC score of {auc_score:.3f} indicates {'excellent' if auc_score > 0.9 else 'good' if auc_score > 0.8 else 'fair'} performance")

# Determine most important feature
most_important_idx = np.argmax(np.abs(model.coef_[0]))
most_important_feature = feature_columns[most_important_idx]
print(f"  • Most important predictor: {most_important_feature}")

print("\n✓ Churn analysis pipeline complete!")
print("=" * 60)
