#!/usr/bin/env bash

# If a command fails, `set -e` will make the whole script exit instead of just
# resuming on the next line.
set -e

# Treat unset variables as errors, and thus exit immediately.
set -u

# Disable filename expansion (“glob”) upon seeing `*`, `?` etc.
set -f

# `set -o pipefail` causes a pipeline (for instance `curl … | …`) to produce a
# failure return code if any command fails. Normally, pipelines only return a
# failure if the last command fails. In combination with `set -e`, this will
# make the script exits if any command in a pipeline fails
set -o pipefail

for hash in $(git log --pretty="tformat:%h" HEAD)
do
	git show --pretty="tformat:%b" "$hash" | grep '^tag' | sed -e 's/^tag:[ \t]*//' -e 's/[ \t]*$//' | while read tag
	do
		git rev-parse "$tag" >/dev/null 2>&1 && git tag -d "$tag"
		git tag "$tag" "$hash" && printf "${tag}\t->\t${hash}\n"
	done
done