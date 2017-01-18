#!/usr/bin/env sh
# This script should be used to synchronize ezplatform-i18n repository
# Translations must be up-to-date in all packages
echo 'Translation synchronization';

echo '# Clean corresponding ezplatform-i18n folder';
rm ./vendor/ezsystems/ezplatform-i18n/platform-ui-bundle/*.xlf
rm ./vendor/ezsystems/ezplatform-i18n/ezpublish-kernel/*.xlf
rm ./vendor/ezsystems/ezplatform-i18n/repository-forms/*.xlf

echo '# Mirror the translation files';
cp ./vendor/ezsystems/platform-ui-bundle/Resources/translations/*.xlf ./vendor/ezsystems/ezplatform-i18n/platform-ui-bundle
cp ./vendor/ezsystems/ezpublish-kernel/eZ/Bundle/EzPublishCoreBundle/Resources/translations/*.xlf ./vendor/ezsystems/ezplatform-i18n/ezpublish-kernel
cp ./vendor/ezsystems/repository-forms/bundle/Resources/translations/*.xlf ./vendor/ezsystems/ezplatform-i18n/repository-forms

echo '# Rename file to fit to crowdin format';
rename 's/\.en\./\./g' ./vendor/ezsystems/ezplatform-i18n/platform-ui-bundle/*
rename 's/\.en\./\./g' ./vendor/ezsystems/ezplatform-i18n/ezpublish-kernel/*
rename 's/\.en\./\./g' ./vendor/ezsystems/ezplatform-i18n/repository-forms/*

echo 'Translation synchronization done !';
