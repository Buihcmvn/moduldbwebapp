// webpack.config.js
const Encore = require('@symfony/webpack-encore');
const path = require('path');
// Add plugin for TypeScript for parallel type checking
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');

if (!Encore.isProduction()) {
    // Adjust the public path as needed for the environment -- dev/production
    Encore.setPublicPath('/build')
        .setOutputPath(__dirname + '/public/build');
}

Encore
    // Set output directory for compiled files
    .setOutputPath(__dirname + '/public/build/')
    .setPublicPath('/build') // Public path for both CSS and JS

    // Add entrypoints for CSS
    .addStyleEntry('login', './assets/styles/login.scss')
    .addStyleEntry('bestellung', './assets/styles/bestellung.scss')

    // Add entrypoints for JS - Updated to use .ts instead of .js
    .addEntry('app', './assets/js/app.ts')

    // Just give CDN or deploy in subdirectory
    .setManifestKeyPrefix('build/')

    // -----------------------------------------------------------
    .enableReactPreset() // <--- React preset

    // -----------------------------------------------------------
    // IMPORTANT: Enable Vue Loader (for Vue 3)
    // Add runtimeCompilerBuild to compile template strings at runtime
    .enableVueLoader(() => {}, { runtimeCompilerBuild: true })

    // Configure plugin to support Vue 3 variables in define
    .configureDefinePlugin(options => {
        options.__VUE_OPTIONS_API__ = true;
        options.__VUE_PROD_DEVTOOLS__ = false;
        options.__VUE_PROD_HYDRATION_MISMATCH_DETAILS__ = false;
    })

    // Enable Source Maps for easier debugging in the browser
    .enableSourceMaps(!Encore.isProduction())

    // -----------------------------------------------------------
    // TypeScript Loader Configuration
    .addRule({
        test: /\.tsx?$/, // Applies this rule to files ending with .ts or .tsx
        exclude: /node_modules/, // Excludes the 'node_modules' directory
        use: [
            {
                loader: 'ts-loader',
                options: {
                    // Let ts-loader treat Vue files as TypeScript
                    appendTsSuffixTo: [/\.vue$/],
                    // This enables TypeScript type checking within the <script lang="ts"> blocks of Vue SFCs
                    transpileOnly: true, // Instructs 'ts-loader' to only transpile (convert) TypeScript to JavaScript
                    happyPackMode: true // Enables HappyPack mode (if HappyPack is installed)
                }
            }
        ]
    })

    .configureBabel((config) => {
        // Make sure Babel is configured to handle modern JS features
        config.plugins.push('@babel/plugin-proposal-class-properties');
        config.plugins.push('@babel/plugin-proposal-private-methods');
    })

    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()

    // Enable Sass/SCSS
    .enableSassLoader()

    // Enable versioning (add unique hash to file name)
    .enableVersioning(Encore.isProduction())

    // -----------------------------------------------------------
    // Add ForkTsCheckerWebpackPlugin plugin
    .addPlugin(new ForkTsCheckerWebpackPlugin({
        typescript: {
            // Path to your tsconfig.json
            configFile: './tsconfig.json',
            // Specify the assets directory to test
            context: path.resolve(__dirname, 'assets'),
            // Configure further if required
            diagnosticOptions: {
                syntactic: true, // Enables checking for syntax errors
                semantic: true, // Enables checking for semantic errors
                declaration: true, // Enables checking errors related to declaration files (.d.ts)
                global: false, // Disables checking for undeclared global variables
            },
            mode: 'write-references', // Improve performance with this mode
        },
        async: true, // Run type checking asynchronously so as not to block the build
        logger: {
            // Configures how the plugin logs messages
            log: (message) => console.log(message),
            error: (message) => console.error(message),
        },
    }));

module.exports = Encore.getWebpackConfig();