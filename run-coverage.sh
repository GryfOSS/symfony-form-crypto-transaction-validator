#!/bin/bash

echo "=== Cryptocurrency Validator Coverage Analysis ==="
echo ""

echo "📊 Generating Coverage Reports..."
echo ""

# Create coverage directory if it doesn't exist
mkdir -p coverage

# Run coverage with multiple output formats
echo "1. Generating HTML Coverage Report..."
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage >/dev/null 2>&1

echo "2. Generating Clover XML Coverage Report..."
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover coverage/clover.xml >/dev/null 2>&1

echo "3. Generating Text Coverage Summary..."
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --colors=never 2>/dev/null > coverage/coverage-summary.txt

echo ""
echo "📈 Coverage Summary:"
echo "-------------------"

# Extract and display coverage summary
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --colors=never 2>/dev/null | grep -A 25 "Code Coverage Report:" | tail -n +3

echo ""
echo "📂 Coverage Reports Generated:"
echo "• HTML Report: coverage/index.html"
echo "• Clover XML: coverage/clover.xml"
echo "• Text Summary: coverage/coverage-summary.txt"
echo ""

echo "🌐 To view HTML coverage report, open: coverage/index.html"
echo ""

# Show file listing
echo "📁 Coverage Files:"
ls -la coverage/

echo ""
echo "✅ Coverage analysis complete!"