#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

# Fetch the JSON data from the specified URL
JSON_DATA=$(curl -s https://api.wordpress.org/core/stable-check/1.0/)

# Use jq to parse the JSON data and extract the version marked as "latest"
TESTED_UP_TO=$(echo "$JSON_DATA" | jq -r 'to_entries[] | select(.value == "latest") | .key')

# Read the TESTED_UP_TO value from config.json
CONFIG_TESTED_UP_TO=$(jq -r '.TESTED_UP_TO' ./config.json)

# Compare the two values and exit if they are the same
if [[ "$TESTED_UP_TO" == "$CONFIG_TESTED_UP_TO" ]]; then
    echo "::debug::Stopped because TESTED_UP_TO has not changed."
    echo "TESTED_UP_TO has not changed. Exiting..."
    exit 0
fi

# Fetch the current STABLE_TAG value from config.json
PREVIOUS_STABLE_TAG=$(jq -r '.STABLE_TAG' config.json)

# Increment the STABLE_TAG value
STABLE_TAG=$(echo "$PREVIOUS_STABLE_TAG" | awk -F. '{$NF+=1} 1' OFS=.)

echo "::debug::TESTED_UP_TO has changed from $CONFIG_TESTED_UP_TO to $TESTED_UP_TO"
echo "::debug::STABLE_TAG has changed from $PREVIOUS_STABLE_TAG to $STABLE_TAG"

# Use sed to replace the version lines in some files
sed -i '' -e "s/Tested up to: [0-9.]*$/Tested up to: $TESTED_UP_TO/" \
          -e "s/Stable tag: [0-9.]*$/Stable tag: $STABLE_TAG/" ./readme.txt

sed -i '' -e "s/Tested up to: [0-9.]*$/Tested up to: $TESTED_UP_TO/" \
          -e "s/Version: [0-9.]*$/Version: $STABLE_TAG/" ./simple-analytics.php

# Get the current date in the specified format
DATE=$(date +"%Y-%m-%d")

# Generate the new changelog item
NEW_ENTRY=$(cat <<EOL
= $STABLE_TAG =
* $DATE
* Upgraded to WordPress $TESTED_UP_TO
EOL
)

# Use sed to insert the new changelog item below the line "== Changelog =="
sed -i '' -e "/== Changelog ==/a\\
\\
= $STABLE_TAG =\\
* $DATE\\
* Upgraded to WordPress $TESTED_UP_TO" ./readme.txt

# Update the config.json file
echo "{
  \"TESTED_UP_TO\": \"$TESTED_UP_TO\",
  \"STABLE_TAG\": \"$STABLE_TAG\"
}" > config.json

# Output the new version information for use in subsequent GitHub Actions steps
echo "::set-output name=tested-up-to::$TESTED_UP_TO"
echo "::set-output name=stable-tag::$STABLE_TAG"
echo "::set-output name=has-changed::true"
