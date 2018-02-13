<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <style type="text/css" media="all">
            /* Media Queries */
            @media only screen and (max-width: 500px) {
                .button-primary {
                    width: 100% !important;
                }
            }
            @font-face {
                font-family: 'Titillium Web';
                src: url('styles/fonts/titillium/titilliumweb-regular-webfont.eot');
                src: url('styles/fonts/titillium/titilliumweb-regular-webfont.eot?#iefix') format('embedded-opentype'),
                     url('styles/fonts/titillium/titilliumweb-regular-webfont.woff') format('woff'),
                     url('styles/fonts/titillium/titilliumweb-regular-webfont.ttf') format('truetype'),
                     url('styles/fonts/titillium/titilliumweb-regular-webfont.svg#titillium_webregular') format('svg');
                font-weight: 400;
                font-style: normal;
            }


            html {
                -webkit-tap-highlight-color: rgba(0,0,0,0);
            }
            body {
                margin: 0;
                padding: 20px;
                background-color: #f5f5f5;
                font-family: 'Titillium Web',sans-serif;
                font-size: 14px;
                line-height: 1.428571429;
                color: #333;
            }
            a {
                color: #3699d2;
                text-decoration: none;
            }
            .email_section {
                margin-bottom: 0;
                padding-left: 0;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                border-color: #e5e5e5;
                margin-bottom: 20px;
                background-color: #fbfbfb;
                border: 1px solid transparent;
                border-radius: 0;
                padding: 15px 20px 20px;
                border-color: #eee;
                border-radius: 0;
                border-width: 1px;
            }

            .email_section h1.section_title {
                font-weight: normal;
                font-size: 18px;
                font-family: inherit;
                line-height: 1.1;
                color: inherit;
                padding-bottom: 9px;
                margin: 0 0 20px;
                border-bottom: 1px solid #eee;
            }

            .email_section p {
                padding: 0 15px;
                margin: 0 0 10px;
            }

            .email_section p.lead {
                font-size: 21px;
                margin-bottom: 20px;
            }
            .button-primary {
                margin-top: 5px;
                padding: 7.5px 16px;
                font-size: 18px;
                line-height: 1.33;
                border-radius: 3px;
                color: #fff;
                background-color: #76b6ec;
                border-color: #76b6ec;
                text-decoration: none;
                display: inline-block;
                margin-bottom: 0;
                font-weight: normal;
                text-align: center;
                vertical-align: middle;
                cursor: pointer;
                background-image: none;
                border: 1px solid transparent;
                white-space: nowrap;
                user-select: none;
                overflow: visible;
            }
            .button-primary:hover {
                background-color: #52a3e7;
                border-color: #52a3e7;
            }
            .copyright {
                text-align: center;
                border-top: 1px solid #eee;
                font-size: 12px;
                padding-top: 15px;
            }
            .button_wrapper.center {
                text-align: center;
            }
            .button_wrapper.center * {
                text-align: left;
            }
            .spacer {
                height: 20px;
            }
            .just_in_case_url {
                border-top: 1px solid #eee;
                font-size: 12px;
                padding-top: 15px;
            }
        </style>
    </head>

    <body>
        <div class="email_section">
            <h1 class="section_title">
                @if (! empty($greeting))
                    {!! $greeting !!}
                @else
                    @if ($level == 'error')
                        Whoops!
                    @else
                        Hello!
                    @endif
                @endif
            </h1>

            <!-- Intro -->
            @foreach ($introLines as $line)
                <p>
                    {!! $line !!}
                </p>
            @endforeach

            <!-- Action Button -->
            @if (isset($actionText))
                <div class="spacer"></div>
                <div class="button_wrapper center">
                    <a href="{!! $actionUrl !!}"
                        class="button-primary"
                        target="_blank">
                        {!! $actionText !!}
                    </a>
                </div>
            @endif

            <div class="spacer"></div>

            <!-- Outro -->
            @foreach ($outroLines as $line)
                <p>
                    {!! $line !!}
                </p>
            @endforeach

            <!-- Sub Copy -->
            @if (isset($actionText))
                <div class="spacer"></div>
                <div class="just_in_case_url">
                    <p>
                        If you’re having trouble clicking the "{!! $actionText !!}" button,
                        copy and paste the URL below into your web browser:
                    </p>

                    <p>
                        <a href="{!! $actionUrl !!}" target="_blank">
                            {!! $actionUrl !!}
                        </a>
                    </p>
                </div>
            @endif

            <div class="spacer"></div>
            
            <div class="copyright">
                &copy; {{ date('Y') }}
                {{ env('APP_NAME') }}&nbsp;&nbsp;|&nbsp;&nbsp;
                All rights reserved.
            </div>
        </div>
    </body>
</html>
