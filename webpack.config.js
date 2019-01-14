const Encore = require('@symfony/webpack-encore');
const path = require('path');
const getEzConfigs = require('./ez.webpack.config.js');
const eZConfigManager = require('./ez.webpack.config.manager.js');
const eZConfigs = getEzConfigs(Encore);

Encore.reset();
Encore.setOutputPath('web/js/')
    .setPublicPath('/js')
    .enableSassLoader()
    .enableSingleRuntimeChunk();

// Put your config here.

// uncomment the two lines below, if you have your own Encore configuration for your project
// const projectConfig = Encore.getWebpackConfig();
// module.exports = [ ...eZConfigs, projectConfig ];

// comment-out this line if you've uncommented the above lines
module.exports = eZConfigs;
