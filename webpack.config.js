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
    // If you can ensure all assets are versioned, you may cache assets longer. In such case it's important that you cache assets
    // longer then the cached dynamic pages referring to them (i.e. cache_ttl: 1d, then 2d for assets to account for stale cache)
    //.enableVersioning()
    //.cleanupOutputBeforeBuild()
    .enableSingleRuntimeChunk();

// Put your config here.

// uncomment the two lines below, if you added a new entry (by Encore.addEntry() or Encore.addStyleEntry() method) to your own Encore configuration for your project
// const projectConfig = Encore.getWebpackConfig();
// module.exports = [ eZConfig, ...customConfigs, projectConfig ];

// comment-out this line if you've uncommented the above lines
module.exports = [ eZConfig, ...customConfigs ];
