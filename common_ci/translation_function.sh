#!/bin/bash
# Pattern to search for (full stop followed by a space)
pattern="\.\s"
# The translation functions to search for, you can add more using a pipe
functions="__|_e"
# Search for calls to translation functions with the pattern in the parameter (excluding brackets and their contents)
matches=$(grep -rnoE "($functions)\([^()]*$pattern[^()]*\)" src/ | sed 's/([^)]*)//g')
# Check for any matches
if [ -n "$matches" ]; then
    echo "Found the following matches:"
    echo "$matches" | awk -F: '{print "Line " $2 " in " $1; printf "--------------------------------------------------------------------------------------\n" }'
    echo "Multiple sentences in a single translation function detected"
    exit 1
else
    echo "No matches found."
fi
