const Encore = require('@symfony/webpack-encore');
const path = require('path');
const getEzConfig = require('./ez.webpack.config.js');
const eZConfigManager = require('./ez.webpack.config.manager.js');
const eZConfig = getEzConfig(Encore);
const customConfigs = require('./ez.webpack.custom.configs.js');

Encore.reset();
Encore.setOutputPath('public/assets/build')
    .setPublicPath('/assets/build')
    .enableSassLoader()
    .enableReactPreset()
    .enableSingleRuntimeChunk();

// Put your config here.
Encore.addEntry('app_default', [
    path.resolve(__dirname, './assets/scss/welcome-page.scss'),
]);

const projectConfig = Encore.getWebpackConfig();
module.exports = [ eZConfig, ...customConfigs, projectConfig ];

// uncomment this line if you've commented-out the above lines
// module.exports = [ eZConfig, ...customConfigs ];
