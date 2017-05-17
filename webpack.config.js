var webpack = require('webpack')
var postcss = require('postcss')
var autoprefixer = require('autoprefixer')
var cssnano = require('cssnano')
var ExtractTextPlugin = require("extract-text-webpack-plugin")
var BrowserSyncPlugin = require('browser-sync-webpack-plugin')
var path = require('path')


var postcssSetup = {
	plugins: () => {return [
		autoprefixer({browsers: 'last 3 versions'}),
		cssnano(),
	]}
}

module.exports = {

    entry: "./src/index.js", // string | object | array
    // Here the application starts executing
    // and webpack starts bundling

    output: {
        path: path.resolve(__dirname, 'dist'), // string
        filename: "[name].js", // string
        publicPath: '../../src/', // string
    },

    module: {
        // configuration regarding modules

        rules: [
            //{ test: /\.js$/, loader: 'source-map', enforce: 'pre' },
            { test: /\.html$/, loader: ['riotjs'], enforce: 'pre' },
            { test: /\.js|\.html$/, loader: 'babel', options: { presets: ['es2015-riot', 'es2017'] }},
            { test: /\.(jpe?g|png|gif)$/i, loader: 'file', options: {name: 'images/[name].[ext]'}},
            { test: /\.ttf$/, loader: `file`, options: {name:'fonts/[name].[ext]'}},
            {
                test: /\.less$/, use: ExtractTextPlugin.extract(
                    [
                        //{loader:'raw'},
                        {loader:'css'},
                        {loader:'postcss', options: postcssSetup},
                        {loader:'less'}
                    ]
                ),
            },
            {
                test: /\.scss$/, use: ExtractTextPlugin.extract(
                    [
                        //{loader:'raw'},
                        {loader:'css'},
                        {loader:'postcss', options: postcssSetup},
                        {loader:'sass'}
                    ]
                ),
            },
        ],

    },

    resolveLoader: {
        moduleExtensions: ["-loader"]
    },

    plugins: [
        new ExtractTextPlugin("css/[name].css"),
		new webpack.ProvidePlugin({
			riot: 'riot',
            regeneratorRuntime: 'regenerator-runtime'
		}),
        new BrowserSyncPlugin({
            // browse to http://localhost:3000/ during development,
            // ./public directory is being served
            host: 'localhost',
            port: 3000,
            files: ['**/*.php', '*.php'],
            //server: { baseDir: ['public'] }
            proxy: 'http://localhost:8888/'
        }),

        // new webpack.optimize.UglifyJsPlugin({
        //     compress: {
        //         sourceMap: true,
        //         warnings: false,
        //     },
        // }),
    ],

    devtool: "source-map", // enum

    context: __dirname, // string (absolute path!)

    target: "web", // enum

    stats: { //object
        assets: true,
        colors: true,
        errors: true,
        //errorDetails: true,
        //hash: true,
    },

    devServer: {

        contentBase: path.join(__dirname, ''), // boolean | string | array, static file location
        compress: true, // enable gzip compression
        historyApiFallback: true, // true for index.html upon 404, object for multiple paths
        https: false, // true for self-signed, object for cert authority
        // ...
    },
}
