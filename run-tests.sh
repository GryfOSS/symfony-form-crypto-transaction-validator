#!/bin/bash

echo "=== Running Cryptocurrency Validator Unit Tests ==="
echo ""

echo "1. Testing Address Validators..."
./vendor/bin/phpunit tests/Unit/Address/ --testdox

echo ""
echo "2. Testing Enum..."
./vendor/bin/phpunit tests/Unit/Enum/ --testdox

echo ""
echo "3. Testing Factory..."
./vendor/bin/phpunit tests/Unit/Factory/ --testdox

echo ""
echo "4. Testing Form Constraints..."
./vendor/bin/phpunit tests/Unit/Form/Constraint/ --testdox

echo ""
echo "5. Testing Form Validators..."
./vendor/bin/phpunit tests/Unit/Form/Validator/ --testdox

echo ""
echo "6. Testing Transaction Validators..."
./vendor/bin/phpunit tests/Unit/Transaction/ --testdox

echo ""
echo "=== Test Summary ==="
./vendor/bin/phpunit --testdox