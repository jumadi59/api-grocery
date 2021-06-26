<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{title}</title>
    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans");

        * {
            box-sizing: border-box;
        }

        body {
            background-color: #fafafa;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .c-email {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 7px 18px 0px rgba(0, 0, 0, 0.1);
        }

        .c-email__header {
            background-color: #0fd59f;
            width: 100%;
            height: 60px;
        }

        .c-email__header__title {
            font-size: 23px;
            font-family: "Open Sans";
            height: 60px;
            line-height: 60px;
            margin: 0;
            text-align: center;
            color: white;
        }

        .c-email__content {
            width: 100%;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            background-color: #fff;
            padding: 15px;
        }

        .c-email__content__text {
            font-size: 17px;
            text-align: center;
            color: #343434;
            margin-top: 0;
        }

        .c-email__code {
            display: block;
            width: fit-content;
            margin: 20px auto;
            background-color: #ddd;
            border-radius: 40px;
            padding: 14px 25px;
            text-align: center;
            font-size: 30px;
            font-family: "Open Sans";
            letter-spacing: 8px;
            box-shadow: 0px 7px 22px 0px rgba(0, 0, 0, 0.1);
        }

        .c-email__footer {
            width: 100%;
            min-height: 60px;
            background-color: #fff;
        }

        .text-title {
            font-family: "Open Sans";
        }

        .text-center {
            text-align: center;
        }

        .text-italic {
            font-style: italic;
        }

        .opacity-30 {
            opacity: 0.3;
        }

        .mb-0 {
            margin-bottom: 0;
        }
    </style>
</head>

<body>

    <div class="c-email">
        <div class="c-email__header">
            <h1 class="c-email__header__title">{title}</h1>
        </div>
        <div class="c-email__content">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                <tbody>
                    <tr>
                        <td style="word-break:break-word;font-size:0px;padding:0px;" align="center">

                            <p class="c-email__content__text text-title">
                                {message}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="word-break:break-word;font-size:0px;padding:0px;" align="center">

                            <div class="c-email__code">
                                <span class="c-email__code__text">{code_verify}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="word-break:break-word;font-size:0px;padding:0px;" align="center">
                            <p class="c-email__content__text text-italic opacity-30 text-title mb-0">{msg_valid}</p>

                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="c-email__footer">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                <tbody>
                    <tr>
                        <td style="word-break:break-word;font-size:0px;padding:0px;" align="center">
                            <div style="cursor:auto;color:#99AAB5;font-family:Whitney, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;font-size:12px;line-height:24px;text-align:center;">
                                Sent by {app_name} • <a href="{base_url}/terms" style="color:#1EB0F4;text-decoration:none;" target="_blank">Terms and Conditions</a> • <a href="{base_url}/faq" style="color:#1EB0F4;text-decoration:none;" target="_blank">FAQ</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="word-break:break-word;font-size:0px;padding:0px;" align="center">
                            <div style="cursor:auto;color:#99AAB5;font-family:Whitney, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;font-size:12px;line-height:24px;text-align:center;">
                                {address}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>