# Check if table exists in database
import pymysql

# Database configuration
db_config = {
    'host': '182.61.1.142',
    'port': 13306,
    'user': 'root',
    'password': 'BtXELjPMb4dadjPy',
    'database': 'ghb7zzwh6fy5j3yl',
    'charset': 'utf8mb4'
}

print("Connecting to database...")
try:
    connection = pymysql.connect(**db_config)
    cursor = connection.cursor()

    # Check customers table structure
    print("\n" + "="*60)
    print("CUSTOMERS TABLE STRUCTURE")
    print("="*60)
    cursor.execute("DESC customers")
    columns = cursor.fetchall()

    print("\nColumn Name              | Type                 | Null | Key")
    print("-" * 70)
    for col in columns:
        print(f"{col[0]:24} | {col[1]:20} | {col[2]:4} | {col[3]}")

    # Check if churn fields exist
    print("\n" + "="*60)
    print("CHURN FIELDS CHECK")
    print("="*60)

    churn_fields = ['months_as_customer', 'order_count', 'days_since_last_order', 'last_order_date', 'churned']
    column_names = [col[0] for col in columns]

    for field in churn_fields:
        if field in column_names:
            print(f"✓ {field}: EXISTS")
        else:
            print(f"✗ {field}: MISSING")

    # Check data count
    print("\n" + "="*60)
    print("DATA COUNT")
    print("="*60)

    cursor.execute("SELECT COUNT(*) FROM customers")
    total = cursor.fetchone()[0]
    print(f"Total customers: {total}")

    cursor.execute("SELECT COUNT(*) FROM customers WHERE email LIKE '%@email.com'")
    test_data = cursor.fetchone()[0]
    print(f"Test customers (@email.com): {test_data}")

    # If churn fields exist, show distribution
    if 'churned' in column_names:
        print("\n" + "="*60)
        print("CHURN DISTRIBUTION")
        print("="*60)

        cursor.execute("""
            SELECT
                CASE WHEN churned = 1 THEN 'Churned' ELSE 'Active' END as status,
                COUNT(*) as count
            FROM customers
            WHERE email LIKE '%@email.com'
            GROUP BY churned
        """)

        for row in cursor.fetchall():
            print(f"{row[0]}: {row[1]}")

    cursor.close()
    connection.close()

    print("\n" + "="*60)
    print("DATABASE CHECK COMPLETE")
    print("="*60)

except Exception as e:
    print(f"\n[ERROR] Database connection failed: {e}")
    print("\nYou can still use CSV data for analysis!")

input("\nPress Enter to exit...")
