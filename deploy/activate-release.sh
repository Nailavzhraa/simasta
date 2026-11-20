#!/usr/bin/env bash
# Install the merged GitHub application, identity integration, and Google configuration.
set -Eeuo pipefail
[[ $EUID -eq 0 && $# -eq 1 ]] || { echo 'Usage: sudo bash activate-release.sh /private/path/google-oauth.json'; exit 1; }
source_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd -P)
python3 "$source_dir/deploy/google/configure.py" "$1"
bash "$source_dir/deploy/identity/activate.sh"
bash "$source_dir/deploy/google/activate.sh" "$1"
echo 'Merged GitHub release, central identities, and Google authorization redirect installed.'
echo 'Complete one real Google sign-in to verify consent and callback.'
