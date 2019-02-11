<?php

// Only valid if PHP7 or greater
//declare(strict_types=1);

/**
 * AUTHOR	   : AVONTURE Christophe.
 *
 * Written date : 11 november 2018
 *
 * Simple Base64 encode/decode javascript interface.
 *
 * Use it online: https://base64.avonture.be/
 *
 * Last mod:
 * 2018-12-31 - Abandonment of jQuery and migration to vue.js
 */

define('REPO', 'https://github.com/cavo789/base64');

// Get the GitHub corner
$github = '';
if (is_file($cat = __DIR__ . DIRECTORY_SEPARATOR . 'octocat.tmpl')) {
    $github = str_replace('%REPO%', REPO, file_get_contents($cat));
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="author" content="Christophe Avonture" />
        <meta name="robots" content="noindex, nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8;" />
        <title>Base64 Encode/Decode</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <style>
            pre {
                white-space: pre-wrap;	   /* css-3 */
                white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
                white-space: -pre-wrap;	  /* Opera 4-6 */
                white-space: -o-pre-wrap;	/* Opera 7 */
                word-wrap: break-word;	   /* Internet Explorer 5.5+ */
            }
            details {
                margin: 1rem;
            }
            summary {
                font-weight: bold;
            }
            textarea {
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <?php echo $github; ?>
        <div class="container">
            <div class="page-header"><h1>Base64 Encode/Decode</h1></div>
            <div class="container" id="app">
                <div class="form-group">
                    <label for="txt">Copy/Paste your text in the
                        textbox below then click on the Process button:</label>
                    <how-to-use demo="https://raw.githubusercontent.com/cavo789/base64/master/images/demo.gif">
                        <ul>
                            <li>Type (or paste) a text in the text area here below and
                                click on the Process button; the text will be encoded.</li>
                            <li>If the text was already encoded, the text will be decoded.</li>
                        </ul>
                    </how-to-use>
                    <textarea class="form-control" rows="10" name="txt" v-model="txt"></textarea>
                </div>
                <button type="button" class="btn btn-primary" @click="processConvert">Process</button>
            </div>
        </div>
        <script src="https://unpkg.com/vue"></script>
        <script type="text/javascript">
            Vue.component('how-to-use', {
                props: {
                    demo: {
                        type: String,
                        required: true
                    }
                },
                template:
                    `<details>
                        <summary>How to use?</summary>
                        <div class="row">
                                <div class="col-sm">
                                    <slot></slot>
                                </div>
                                <div class="col-sm"><img v-bind:src="demo" alt="Demo"></div>
                            </div>
                        </div>
                    </details>`
            });

            var app = new Vue({
                el: '#app',
                data: {
                    txt: '✓ The quick brown fox jumps over the lazy dog'
                },
                methods: {
                    processConvert() {
                        var $result = '';

                        // Use a regex to detect if the given string is a base64 one or not
                        // @see https://stackoverflow.com/a/35002237/1065340
                        var $is64 = false;

                        var base64regex = /^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$/;

                        try {
                            $is64 = base64regex.test(this.txt);
                            // The check isn't reliable enough; f.i. the word "mindmaps" will be detected a
                            // an base64 encoded string while it isn't ("mindmap" (without final "s" is ok))
                            if ($is64) {
                                // Make sure, try to decode the string; if an error occurred, then it wasn't
                                // a base64 encoded string.
                                try {
                                    $result = decodeURIComponent(escape(window.atob(this.txt)));
                                } catch (error) {
                                    $is64 = false;
                                }
                            }
                        } catch(error) {
                        }

                        if (!$is64) {
                            // Correctly handle unicode
                            // @see https://developer.mozilla.org/en-US/docs/Web/API/WindowBase64/Base64_encoding_and_decoding#The_Unicode_Problem
                            try {
                                //console.log('Convert from ASCII to base64');
                                $result = window.btoa(unescape(encodeURIComponent(this.txt)));
                            } catch (error) {
                                console.log(error);
                            }
                        } else {
                            try {
                                //console.log('Convert from base64 to ASCII');
                                $result = decodeURIComponent(escape(window.atob(this.txt)));
                            } catch (error) {
                            }
                        }

                        if ($result !== '') {
                            // $result will be empty in case of conversion error
                            this.txt = $result;
                        }
                    }
                }
            });
        </script>
    </body>
</html>
