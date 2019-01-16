const path = require('path');
const bundles = require('./var/encore/ez.config.js');

module.exports = (Encore) => {
    return bundles.map((configPath) => {
        const getConfig = require(configPath);

        Encore.reset();
        Encore.setOutputPath('web/assets/build')
            .setPublicPath('/assets/build')
            .addExternals({
                react: {
                    root: 'React',
                    commonjs2: 'react',
                    commonjs: 'react',
                    amd: 'react',
                },
                'react-dom': {
                    root: 'ReactDOM',
                    commonjs2: 'react-dom',
                    commonjs: 'react-dom',
                    amd: 'react-dom',
                },
                jquery: {
                    root: 'jQuery',
                    commonjs2: 'jquery',
                    commonjs: 'jquery',
                    amd: 'jquery',
                },
                moment: {
                    root: 'moment',
                    commonjs2: 'moment',
                    commonjs: 'moment',
                    amd: 'moment',
                },
                'popper.js': {
                    root: 'Popper',
                    commonjs2: 'popper',
                    commonjs: 'popper',
                    amd: 'popper',
                },
                alloyeditor: {
                    root: 'AlloyEditor',
                    commonjs2: 'AlloyEditor',
                    commonjs: 'AlloyEditor',
                    amd: 'AlloyEditor',
                },
                'prop-types': {
                    root: 'PropTypes',
                    commonjs2: 'prop-types',
                    commonjs: 'prop-types',
                    amd: 'prop-types',
                }
            })
            .enableSassLoader()
            .enableReactPreset()
            .enableSingleRuntimeChunk();

        return getConfig(Encore);
    });
};
