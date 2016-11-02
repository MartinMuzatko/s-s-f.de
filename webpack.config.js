var webpack = require('webpack')
var autoprefixer = require('autoprefixer')
var BrowserSyncPlugin = require('browser-sync-webpack-plugin')

module.exports = {
    entry: './index.js',
    output: {
        path: './',
        filename: 'bundle.js',
        sourceMapFilename: "[name].js.map",
    },
    module: {
        preLoaders: [
            { test: /\.html$/, loader: 'riotjs' },
            { test: /\.js$/, loader: 'eslint!source-map' },
        ],
        loaders: [
            { test: /\.md$/, loader: 'html!markdown'},
            { test: /\.(jpe?g|png|gif|svg|mp4)$/i, loader: 'file'},
            { test: /\.html$|\.js$/, loader: 'babel', query: { presets: 'es2015-riot' }},
            { test: /\.less$/, loader: 'style!css?minimize!postcss!less'},
        ]
    },
    postcss: () => {
        return [
            autoprefixer({browsers: 'last 2 versions'})
        ];
    },
    plugins: [
        new webpack.ProvidePlugin({
            riot: 'riot'
        }),
        new BrowserSyncPlugin(
            {
                host: 'localhost',
                port: 8081,
                proxy: 'http://localhost:8080/'
            },
            {
                reload: true
            }
        )
    ],
    eslint: {
        configFile: './.eslintrc'
    },
    devtool: 'source-map'
}
