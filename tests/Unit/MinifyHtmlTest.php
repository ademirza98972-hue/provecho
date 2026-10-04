<?php

namespace Tests\Unit;

use App\Http\Middleware\MinifyHtml;
use PHPUnit\Framework\TestCase;

class MinifyHtmlTest extends TestCase
{
    public function test_minify_keeps_scripts_pre_and_css_math_working(): void
    {
        $html = <<<'HTML'
        <!doctype html>
        <html>
            <head>
                <style>
                    /* komentar */
                    .a  >  .b { width : calc(100% - 20px) ;  }
                </style>
            </head>
            <body>
                <!-- dibuang -->
                <b>satu</b>   <i>dua</i>
                <pre>  baris 1
          baris 2</pre>
                <script>
                    // komentar baris
                    const x = 1;
                </script>
            </body>
        </html>
        HTML;

        $out = MinifyHtml::minify($html);

        $this->assertStringNotContainsString('dibuang', $out);
        $this->assertStringNotContainsString('komentar */', $out);
        $this->assertStringContainsString('<b>satu</b> <i>dua</i>', $out, 'spasi antar elemen inline harus tetap ada');
        // heredoc membuang 8 spasi indentasi, jadi isi <pre> aslinya "  baris 1\n  baris 2"
        $this->assertStringContainsString("<pre>  baris 1\n  baris 2</pre>", $out);
        $this->assertStringContainsString('calc(100% - 20px)', $out);
        $this->assertStringContainsString('.a>.b{width : calc(100% - 20px)}', $out);
        $this->assertMatchesRegularExpression("#// komentar baris\nconst x = 1;#", $out, 'komentar // tidak boleh menelan kode setelahnya');
        $this->assertStringNotContainsString("\n        <b>", $out);
    }
}
