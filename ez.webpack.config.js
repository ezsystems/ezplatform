const path = require('path');
const bundles = require('./var/encore/ez.config.js');

module.exports = (Encore) => {
    return bundles.map((configPath) => {
        const getConfig = require(configPath);

        Encore.reset();
        Encore.setOutputPath('web/assets/build')
            .setPublicPath('/assets/build')
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

        return getConfig(Encore);
    });
};
