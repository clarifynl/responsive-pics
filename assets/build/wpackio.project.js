const TerserPlugin = require('terser-webpack-plugin');
const pkg = require('../../package.json');

module.exports = {
	appName: 'responsivePics',
	type: 'plugin',
	slug: 'responsive-pics',
	bannerConfig: {
		name: 'ResponsivePics',
		author: 'Booreiland',
		link: 'https://responsive.pics',
		version: pkg.version,
		copyrightText: 'This software is released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE)',
		credit: false
	},
	files: [
		{
			name: 'focalpoint',
			entry: {
				admin: [
					'./assets/scripts/focalpoint.js',
					'./assets/styles/focalpoint.scss'
				]
			},
			webpackConfig: {
				optimization: {
					minimize: true,
					minimizer: [
						new TerserPlugin({
							terserOptions: {
								compress: {
									drop_console: false
								}
							}
						})
					]
				}
			}
		}
	],
	outputPath: 'dist',
	hasReact: false,
	disableReactRefresh: false,
	hasSass: true,
	hasLess: false,
	hasFlow: false,
	externals: {
		jquery: 'jQuery',
	},
	alias: undefined,
	errorOverlay: true,
	optimizeSplitChunks: true,
	watch: '(./src|lib/**/*.php)',
	packageFiles: [
		'src/**',
		'lib/**',
		'vendor/**',
		'dist/**',
		'composer.json',
		'*.php',
		'*.md',
		'LICENSE',
		'*.css'
	],
	packageDirPath: 'package'
};
