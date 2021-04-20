<?php
/**
 * Test Enqueue API.
 *
 * @package WPackio\Test
 */

namespace WPackioTest;

class EnqueueTest extends TestCase {
	/**
	 * Mocked Template Directory Uri
	 * @var string
	 */
	protected $templateDirectoryUri = 'http://example.com/path/to/template/directory';

	/**
	 * Mocked Stylesheet Directory Uri
	 * @var string
	 */
	protected $stylesheetDirectoryUri = 'http://example.com/path/to/child_theme_template/directory';

	/**
	 * Mocked Template Directory path.
	 * @var string
	 */
	protected $templateDirectory = __DIR__ . '/../data';

	/**
	 * Mocked Stylesheet Directory path.
	 * @var string
	 */
	protected $stylesheetDirectory = __DIR__ . '/../data';

	/**
	 * Mocked Plugin Uri.
	 * @var string
	 */
	protected $pu = 'http://example.com/path/to/plugin/dist';

	/**
	 * Mocked Plugin Path.
	 * @var string
	 */
	protected $pp = __DIR__ . '/../data/plugin.php';

	public function setUp() {
		parent::setUp();
		// Stub out a few functions we will need
		// with predefined output
		\Brain\Monkey\Functions\stubs([
			'get_template_directory' => $this->templateDirectory,
			'get_template_directory_uri' => $this->templateDirectoryUri,
			'get_stylesheet_directory' => $this->stylesheetDirectory,
			'get_stylesheet_directory_uri' => $this->stylesheetDirectoryUri,
			'plugins_url' => $this->pu,
		]);
		// Stub some returnFirstArguments function
		\Brain\Monkey\Functions\stubs([
			'esc_js',
			'wp_parse_args',
			'sanitize_title_with_dashes'
		]);
	}

	/**
	 * @testdox Constructor adds WordPress actions
	 */
	public function test_construct() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', '/plugin/path/plugin.php' );
		// We expect hooks on both wp_head and admin_head
		$this->assertEquals( has_action( 'wp_head', 'WPackio\\Enqueue->printPublicPath()', -1000 ), -1000 );
		$this->assertEquals( has_action( 'admin_print_scripts', 'WPackio\\Enqueue->printPublicPath()', -1000 ), -1000 );
	}

	/**
	 * @testdox Constructor throws exception on invalid $type
	 */
	public function test_construct_throws_on_invalid_type() {
		$this->expectException('\LogicException');
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'aasdasd', '/plugin/path/plugin.php' );
	}

	/**
	 * @testdox `printPublicPath` works for plugins
	 */
	public function test_printPublicPath_for_plugin() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', '/plugin/path/plugin.php' );
		ob_start();
		$enqueue->printPublicPath();
		$result = ob_get_clean();
		$this->assertContains( 'window.__wpackIofoodist=\'' . $this->pu . '/\'', $result );
	}

	/**
	 * @testdox `printPublicPath` works for regular themes
	 */
	public function test_printPublicPath_for_regular_theme() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'regular' );
		ob_start();
		$enqueue->printPublicPath();
		$result = ob_get_clean();
		$this->assertContains( 'window.__wpackIofoodist=\'' . $this->templateDirectoryUri . '/dist/\'', $result );
	}

	/**
	 * @testdox `printPublicPath` works child themes
	 */
	public function test_printPublicPath_for_child_theme() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'child' );
		ob_start();
		$enqueue->printPublicPath();
		$result = ob_get_clean();
		$this->assertContains( 'window.__wpackIofoodist=\'' . $this->stylesheetDirectoryUri . '/dist/\'', $result );
	}

	/**
	 * @testdox `printPublicPath` works with sub-folders in outputPath
	 */
	public function test_printPublicPath_with_subfolders() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'assets/Dist', '1.0.0', 'theme', false, 'regular' );
		ob_start();
		$enqueue->printPublicPath();
		$result = ob_get_clean();
		$this->assertContains( 'window.__wpackIofooassetsDist=\'' . $this->templateDirectoryUri . '/assets/Dist/\'', $result );
	}

	/**
	 * @testdox `printPublicPath` adheres to character case
	 */
	public function test_printPublicPath_adheres_to_character_case() {
		$enqueue = new \WPackio\Enqueue(
			'foo',
			'AsSets/&*SubFolder//Dist',
			'1.0.0',
			'plugin',
			'/plugin/path/plugin.php'
		);
		\ob_start();
		$enqueue->printPublicPath();
		$result = \ob_get_clean();
		$this->assertContains(
			'window.__wpackIofooAsSetsSubFolderDist=\'' . $this->pu . '/\'',
			$result
		);
	}

	/**
	 * @testdox `getUrl` works for regular themes
	 */
	public function test_getUrl_for_regular_theme() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'regular' );
		$this->assertEquals( $this->templateDirectoryUri . '/dist/app/main.js', $enqueue->getUrl( 'app/main.js' ) );
	}

	/**
	 * @testdox `getUrl` works for child themes
	 */
	public function test_getUrl_for_child_theme() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'child' );
		$this->assertEquals( $this->stylesheetDirectoryUri . '/dist/app/main.js', $enqueue->getUrl( 'app/main.js' ) );
	}

	/**
	 * @testdox `getUrl` works for plugins
	 */
	public function test_getUrl_for_plugin() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', '/path/to/plugin.php' );
		$this->assertEquals( $this->pu . '/app/main.js', $enqueue->getUrl( 'app/main.js' ) );
	}

	/**
	 * @testdox `getManifest` works if file is valid
	 */
	public function test_getManifest() {
		$path_to_manifest = dirname( $this->pp ) . '/dist/app/manifest.json';
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );
		$manifest = $enqueue->getManifest( 'app' );
		$this->assertEquals( json_decode( file_get_contents( $path_to_manifest ), true ), $manifest );
		$this->assertMatchesSnapshot( $manifest );
	}

	/**
	 * @testdox `getManifest` throws if file not found
	 */
	public function test_getManifest_throws_if_file_not_found() {
		$this->expectException( '\LogicException' );
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );
		$enqueue->getManifest( 'noop' );
	}

	/**
	 * @testdox `getManifest` throws if file not valid JSON
	 */
	public function test_getManifest_throws_if_file_not_valid() {
		$this->expectException( '\LogicException' );
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );
		$enqueue->getManifest( 'broken' );
	}

	/**
	 * @testdox `getAssets` throws on invalid entrypoint
	 */
	public function test_getAssets_throws_on_invalid_entrypoint() {
		$this->expectException('\LogicException');
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'regular' );
		$enqueue->getAssets( 'app', 'noop', [] );
	}

	/**
	 * @testdox `getAssets` works for regular themes
	 */
	public function test_getAssets_for_regular_theme() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'regular' );
		$assets = $enqueue->getAssets( 'app', 'main', [
			'js' => true,
			'css' => true,
			'js_dep' => [],
			'css_dep' => [],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		] );
		// expect on js
		$this->assertArrayHasKey( 'js', $assets );
		foreach ( $assets['js']  as $js ) {
			$this->assertArrayHasKey( 'url', $js );
			$this->assertArrayHasKey( 'handle', $js );
			$this->assertContains( $this->templateDirectoryUri . '/dist/app/', $js['url'] );
		}


		// expect on js
		$this->assertArrayHasKey( 'css', $assets );
		foreach ( $assets['css']  as $css ) {
			$this->assertArrayHasKey( 'url', $css );
			$this->assertArrayHasKey( 'handle', $css );
			$this->assertContains( $this->templateDirectoryUri . '/dist/app/', $css['url'] );
		}

		$this->assertMatchesSnapshot( $assets );
	}

	/**
	 * @testdox `getAssets` works for child themes
	 */
	public function test_getAssets_for_child_theme() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'theme', false, 'child' );
		$assets = $enqueue->getAssets( 'app', 'main', [
			'js' => true,
			'css' => true,
			'js_dep' => [],
			'css_dep' => [],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		] );
		// expect on js
		$this->assertArrayHasKey( 'js', $assets );
		foreach ( $assets['js']  as $js ) {
			$this->assertArrayHasKey( 'url', $js );
			$this->assertArrayHasKey( 'handle', $js );
			$this->assertContains( $this->stylesheetDirectoryUri . '/dist/app/', $js['url'] );
		}


		// expect on js
		$this->assertArrayHasKey( 'css', $assets );
		foreach ( $assets['css']  as $css ) {
			$this->assertArrayHasKey( 'url', $css );
			$this->assertArrayHasKey( 'handle', $css );
			$this->assertContains( $this->stylesheetDirectoryUri . '/dist/app/', $css['url'] );
		}

		$this->assertMatchesSnapshot( $assets );
	}

	/**
	 * @testdox `getAssets` works for plugins
	 */
	public function test_getAssets_for_plugin() {
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );
		$assets = $enqueue->getAssets( 'app', 'main', [
			'js' => true,
			'css' => true,
			'js_dep' => [],
			'css_dep' => [],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		] );
		// expect on js
		$this->assertArrayHasKey( 'js', $assets );
		foreach ( $assets['js']  as $js ) {
			$this->assertArrayHasKey( 'url', $js );
			$this->assertArrayHasKey( 'handle', $js );
			$this->assertContains( $this->pu . '/app/', $js['url'] );
		}

		// expect on js
		$this->assertArrayHasKey( 'css', $assets );
		foreach ( $assets['css']  as $css ) {
			$this->assertArrayHasKey( 'url', $css );
			$this->assertArrayHasKey( 'handle', $css );
			$this->assertContains( $this->pu . '/app/', $css['url'] );
		}

		$this->assertMatchesSnapshot( $assets );
	}

	/**
	 * @testdox `getAssets` has same handle for every runtime asset
	 */
	public function test_getAssets_has_same_handle_for_every_runtime() {
		$runtime_handles = [];
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );
		$ass_main = $enqueue->getAssets( 'app', 'main', [
			'js' => true,
			'css' => true,
			'js_dep' => [],
			'css_dep' => [],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		] );
		$ass_mobile = $enqueue->getAssets( 'app', 'mobile', [
			'js' => true,
			'css' => true,
			'js_dep' => [],
			'css_dep' => [],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		] );

		foreach ( $ass_main['js'] as $js ) {
			if ( strpos( $js['url'], 'runtime.js' ) !== false ) {
				$runtime_handles[] = $js['handle'];
			}
		}
		foreach ( $ass_mobile['js'] as $js ) {
			if ( strpos( $js['url'], 'runtime.js' ) !== false ) {
				$runtime_handles[] = $js['handle'];
			}
		}

		$this->assertTrue(
			count( array_unique( $runtime_handles ) ) === 1
			&& end( $runtime_handles ) === $enqueue->getHandle( 'app', 'app/runtime.js', 'script' )
		);
	}

	/**
	 * @testdox `register` calls proper WordPress APIs
	 */
	public function test_register() {
		// Get the manifest beforehand for assertion
		$path_to_manifest = dirname( $this->pp ) . '/dist/app/manifest.json';
		$manifest = json_decode( file_get_contents( $path_to_manifest ), true );

		// Prepare
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );

		// Loop over all js and make sure wp_register_script is called
		$js_deps = [];
		$css_deps = [];

		foreach ( $manifest['wpackioEp']['main']['js'] as $js_path ) {
			$js_handle = $enqueue->getHandle( 'app', $js_path, 'script' );
			\Brain\Monkey\Functions\expect( 'wp_register_script' )
				->once()
				->with( $js_handle, $this->pu . '/' . $js_path, array_merge( [ 'jquery' ], $js_deps ), '1.0.0', true );
			$js_deps[] = $js_handle;
		}
		// Loop over all css and make sure wp_register_style is called
		foreach ( $manifest['wpackioEp']['main']['css'] as $css_path ) {
			$css_handle = $enqueue->getHandle( 'app', $css_path, 'style' );
			\Brain\Monkey\Functions\expect( 'wp_register_style' )
				->once()
				->with( $css_handle, $this->pu . '/' . $css_path, array_merge( [ 'ui' ], $css_deps ), '1.0.0', 'all' );
			$css_deps[] = $css_handle;
		}


		$enqueue_assets = $enqueue->register( 'app', 'main', [
			'js' => true,
			'css' => true,
			'js_dep' => [ 'jquery' ],
			'css_dep' => [ 'ui' ],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		] );

		$this->assertMatchesSnapshot( $enqueue_assets );
	}

	/**
	 * @testdox `enqueue` calls proper WordPress APIs
	 */
	public function test_enqueue() {
		// Arrange
		$config = [
			'js' => true,
			'css' => true,
			'js_dep' => [ 'jquery' ],
			'css_dep' => [ 'ui' ],
			'identifier' => false,
			'in_footer' => true,
			'media' => 'all',
		];

		$expected_return = [
			'js' => [
				[
					'handle' => 'js_foo',
				],
				[
					'handle' => 'js_bar',
				],
			],
			'css' => [
				[
					'handle' => 'css_foo',
				],
				[
					'handle' => 'css_bar',
				],
			],
		];

		// Assert
		$enqueue = $this->getMockBuilder(\WPackio\Enqueue::class)
			->setConstructorArgs( [ 'foo', 'dist', '1.0.0', 'plugin', $this->pp ] )
			->setMethods( [ 'register' ] )
			->getMock();

		$enqueue->expects( $this->once() )
			->method( 'register' )
			->with( 'app', 'main', $config )
			->willReturn( $expected_return );

		foreach ( $expected_return['js'] as $js ) {
			\Brain\Monkey\Functions\expect( 'wp_enqueue_script' )
				->once()
				->with( $js['handle'] );
		}
		foreach ( $expected_return['css'] as $css ) {
			\Brain\Monkey\Functions\expect( 'wp_enqueue_style' )
				->once()
				->with( $css['handle'] );
		}

		// Act
		$enqueue->enqueue( 'app', 'main', $config );
	}

	/**
	 * @testdox `getHandle` throws exception on invalid type
	 */
	public function test_getHandle_throws_exception() {
		$this->expectException( '\LogicException' );
		$enqueue = new \WPackio\Enqueue( 'foo', 'dist', '1.0.0', 'plugin', $this->pp );
		$enqueue->getHandle( 'foo', 'bar', 'baz' );
	}
}
