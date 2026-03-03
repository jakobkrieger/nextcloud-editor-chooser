const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
    fileaction: path.join(__dirname, 'src', 'fileaction.js'),
    settings: path.join(__dirname, 'src', 'settings.js'),
}

// Disable source maps in production to avoid 404 errors on .map files
if (process.env.NODE_ENV === 'production') {
    webpackConfig.devtool = false
}

module.exports = webpackConfig
