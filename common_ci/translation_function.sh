#!/bin/bash
# Pattern to search for in translation functions
pattern="\([^)]* \([^)]*\) [^.]*\.\s[^)]*\)|\([^()]*\.\s[^()]*\)"
# The translation functions to search for, you can add more using a pipe
functions="\s__|\s_e"
# Exclude at least two dots and a space seen
exclude_dots="\.\.\s"
# Word to exclude from the search (e.g., N.B., etc)
exclude_word="[A-Za-z]\.[A-Za-z]\.\s"
# Search for calls to translation functions with the pattern in the parameter
filtered_matches=$(grep -rnE "($functions)($pattern)" src/ | sed "s/$exclude_word//g; s/$exclude_dots//g")
# Further check
matches=$(echo "$filtered_matches" | grep -E "($functions)($pattern)")
# Check for any matches
if [ -n "$matches" ]; then
    echo "Found the following matches:"
    echo "$matches" | awk -F: '{print "Line " $2 " in " $1; printf "--------------------------------------------------------------------------------------\n" }'
    echo "Multiple sentences in a single translation function detected"
    exit 1
else
    echo "No matches found."
fi
