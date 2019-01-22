const path = require('path');
const bundles = require('./var/encore/ez.config.js');

module.exports = (Encore) => {
    Encore.setOutputPath('web/assets/ez/build')
        .setPublicPath('/assets/ez/build')
        .addExternals({
            react: 'React',
            'react-dom': 'ReactDOM',
            jquery: 'jQuery',
            moment: 'moment',
            'popper.js': 'Popper',
            alloyeditor: 'AlloyEditor',
            'prop-types': 'PropTypes',
        })
        .enableSassLoader()
        .enableReactPreset()
        .enableSingleRuntimeChunk();

    bundles.forEach((configPath) => {
        const addEntries = require(configPath);

        addEntries(Encore);
    });

    const eZConfig = Encore.getWebpackConfig();

    eZConfig.name = 'ez';

    return eZConfig;
};
