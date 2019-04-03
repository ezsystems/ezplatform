const path = require('path');
const customConfigs = require('./var/encore/ez.webpack.custom.config.js');

module.exports = customConfigs.reduce((configs, customConfigPath) => {
    let customConfig = require(customConfigPath);

    if (!Array.isArray(customConfig)) {
        customConfig = [customConfig];
    }

    return [ ...configs, ...customConfig ];
}, []);
