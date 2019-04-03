const Encore = require('@symfony/webpack-encore');
const path = require('path');
const getEzConfig = require('./ez.webpack.config.js');
const eZConfigManager = require('./ez.webpack.config.manager.js');
const eZConfig = getEzConfig(Encore);
const customConfigs = require('./ez.webpack.custom.configs.js');

Encore.reset();
Encore.setOutputPath('web/assets/build')
    .setPublicPath('/assets/build')
    .enableSassLoader()
    .enableReactPreset()
    .enableSingleRuntimeChunk();

// Put your config here.

// uncomment the two lines below, if you added a new entry (by Encore.addEntry() or Encore.addStyleEntry() method) to your own Encore configuration for your project
// const projectConfig = Encore.getWebpackConfig();
// module.exports = [ eZConfig, ...customConfigs, projectConfig ];

// comment-out this line if you've uncommented the above lines
module.exports = [ eZConfig, ...customConfigs ];
