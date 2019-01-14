const path = require('path');
const fs = require('fs');
const eZSystemsPath = path.resolve('./vendor/ezsystems/');
const bundles = [];

fs.readdirSync(eZSystemsPath).forEach((file) => {
    const configPath = path.resolve(eZSystemsPath, file, 'ez.webpack.config.js');

    if (fs.existsSync(configPath)) {
        bundles.push(require(configPath));
    }
});

module.exports = (Encore) => {
    return bundles.map((getConfig) => {
        Encore.reset();
        Encore.setOutputPath('web/js/')
            .setPublicPath('/js')
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
            })
            .enableSassLoader()
            .enableSingleRuntimeChunk();

        return getConfig(Encore);
    });
};
