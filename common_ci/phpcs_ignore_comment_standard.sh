#THIS IS TO ENSURE THAT THE DEVELOPERS FOLLOW THE RIGHT COMMENT PATTERN WHILE USING phpcs:ignore
#!/bin/bash

######################################################################
##THIS SCRIPT SEARCHES FOR THE WRONG COMMENT FOR phpcs:ignore USED##
######################################################################
#result_wrong=$(grep -rn 'phpcs:ignore(?!.*--.*(\S+))' src/) #To be tested later for pcre standard
result_wrong=$(grep -Rn "// phpcs:ignore [^[:space:]]*$" src/*)
echo  "PHPCS IGNORE LINES MISSING COMMENTS: "
echo "$result_wrong" | awk -F: '{print "Line " $2 " in " $1; printf "--------------------------------------------------------------------------------------\n" }'

######################################################################
##THIS SCRIPT FAILS THE JOB IF ANY WRONG PATTERN IS FOUND##
######################################################################
if [ -n "$result_wrong" ]; then
  echo  "Failed: There are incorrect phpcs:ignore comments pattern. Correct pattern should be "// phpcs:ignore [rule ignored] -- [reason ignored]""
  exit 1
fi
