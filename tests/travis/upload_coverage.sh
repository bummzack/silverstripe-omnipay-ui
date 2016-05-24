#!/usr/bin/env bash
echo "coverage = $COVERAGE, slug = $TRAVIS_REPO_SLUG, commit = $TRAVIS_COMMIT"
if [ "$COVERAGE" -eq 1 ]; then
    cd omnipay-ui
	wget https://scrutinizer-ci.com/ocular.phar
	php ocular.phar code-coverage:upload -v --format=php-clover ~/builds/ss/omnipay-ui/coverage.clover
fi
