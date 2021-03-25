var ExtractTextPlugin = require("extract-text-webpack-plugin");
var path = require("path");

module.exports = {
  entry: {
    app: "./src/index.jsx"
  },
  output: {
    path: path.resolve(__dirname, "dist"),
    filename: "adminscript.js"
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: ExtractTextPlugin.extract({
          fallback: "style-loader",
          use: ["css-loader", "sass-loader"],
          publicPath: "dist"
        })
      },
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use:[
                {
                  loader: 'babel-loader',
                  options: {
                    presets: ["@babel/env", "@babel/react"],
                    plugins:  ["@babel/plugin-proposal-class-properties"],
                  }
                }
            ],
      },
      {
        test: /\.(jpe?g|png|gif|svg)$/i,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[ext]',
              outputPath: '/'
            }
          }
        ]
      },
      {
        test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[ext]',
              outputPath: '/'
            }
          }
        ]
      },      
    ]
  },
  resolve: {
    extensions: [".js", ".jsx"]
  },
  plugins: [
    new ExtractTextPlugin({
      filename: "style.css",
      allChunks: true
    })
  ]
};

