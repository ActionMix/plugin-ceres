const path = require("path");
const VueLoaderPlugin = require("vue-loader/lib/plugin");

module.exports = env =>
{
    env = env || {};
    return {
        name: "scripts",
        mode: env.prod ? "production" : "development",
        entry: {
            category: "./resources/js/src/category.js",
            item: "./resources/js/src/item.js",
            checkout: "./resources/js/src/checkout.js"
        },
        output: {
            filename: "ceres-[name]" + (env.prod ? ".min" : "") + ".js",
            path: path.resolve(__dirname, "..", "..", "resources/js/dist/"),
            publicPath: 'https://s3-eu-central-1.amazonaws.com/plentymarkets-public-92/jpx0tvae1136/plugin/27/ceres/js/dist/'
        },
        resolve: {
            alias: {
                vue: "vue/dist/vue" + (env.prod ? ".min" : "") + ".js"
            }
        },
        devtool: "source-map",
        module: {
            rules: [
                {
                    enforce: "pre",
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: "eslint-loader",
                    options: {
                        cache: true,
                        fix: env.prod
                    }
                },
                {
                    test: require.resolve("jquery"),
                    use: [
                        {
                            loader: "expose-loader",
                            options: "$"
                        },
                        {
                            loader: "expose-loader",
                            options: "jQuery"
                        }
                    ]
                },
                {
                    test: /\.vue$/,
                    loader: "vue-loader"
                },
                {
                    test: /\.m?js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: "babel-loader"
                    }
                }
            ]
        },
        plugins: [
            new VueLoaderPlugin()
        ],
    };
};
