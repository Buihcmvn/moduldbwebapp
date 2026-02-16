// webpack.config.js
const Encore = require('@symfony/webpack-encore');
const path = require('path');
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');

if (!Encore.isProduction()) {
    Encore.setPublicPath('/build')
          .setOutputPath(path.resolve(__dirname, 'public/build'));
}

Encore
    // Thư mục đầu ra cho các file đã biên dịch
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Các điểm đầu vào (Entry points)
    .addStyleEntry('login', './assets/styles/login.scss')
    .addStyleEntry('bestellung', './assets/styles/bestellung.scss')
    .addEntry('app', './assets/js/app.ts')

    .setManifestKeyPrefix('build/')

    // Các tính năng hỗ trợ Framework
    .enableReactPreset()
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })

    .configureDefinePlugin(options => {
        options.VUE_OPTIONS_API = true;
        options.VUE_PROD_DEVTOOLS = false;
        options.VUE_PROD_HYDRATION_MISMATCH_DETAILS = false;
    })

    .enableSourceMaps(!Encore.isProduction())
    .enableSassLoader()
    .enableVersioning(Encore.isProduction())
    .enableSingleRuntimeChunk()

    // Cấu hình TypeScript Loader
    .addRule({
        test: /\.tsx?$/,
        exclude: /node_modules/,
        use: [
            {
                loader: 'ts-loader',
                options: {
                    appendTsSuffixTo: [/\.vue$/],
                    transpileOnly: true,
                    happyPackMode: true
                }
            }
        ]
    })

    .addPlugin(new ForkTsCheckerWebpackPlugin({
        typescript: {
            configFile: './tsconfig.json',
            diagnosticOptions: {
                semantic: true,
                syntactic: true,
            },
        },
        async: true
    }));

module.exports = Encore.getWebpackConfig();