<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход - SONGLY</title>
  <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Krona One', sans-serif;
      background-color: #303030;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .login-container {
      background: #404040;
      border: 2px solid #ffffff;
      border-radius: 0;
      padding: 60px 50px;
      max-width: 500px;
      width: 100%;
    }

    .logo {
      text-align: center;
      margin-bottom: 50px;
    }

    .logo-text {
      font-size: 36px;
      color: #ffffff;
      text-decoration: none;
      letter-spacing: 2px;
    }

    h1 {
      text-align: center;
      color: #ffffff;
      margin-bottom: 15px;
      font-size: 24px;
      font-weight: normal;
    }

    .subtitle {
      text-align: center;
      color: #cccccc;
      margin-bottom: 50px;
      font-size: 14px;
    }

    #vk-auth-widget {
      display: flex;
      justify-content: center;
      margin: 30px 0;
      min-height: 48px;
    }

    #vk-auth-widget iframe {
      border-radius: 0 !important;
    }

    .info-text {
      text-align: center;
      color: #cccccc;
      font-size: 12px;
      line-height: 1.6;
      margin-top: 40px;
      padding-top: 30px;
      border-top: 1px solid #666666;
    }

    .back-link {
      text-align: center;
      margin-top: 30px;
    }

    .back-link a {
      color: #ffffff;
      text-decoration: none;
      font-size: 14px;
      border: 1px solid #ffffff;
      padding: 10px 30px;
      display: inline-block;
      transition: background-color 0.3s;
    }

    .back-link a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="logo">
      <a href="../index.php" class="logo-text">SONGLY</a>
    </div>

    <h1>ВХОД</h1>
    <p class="subtitle">Войдите через ВКонтакте</p>

    <?php require_once '../config/vk_config.php'; ?>

    <!-- VK ID SDK Widget Container -->
    <div id="vk-auth-widget"></div>

    <script src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
    <script type="text/javascript">
      if ('VKIDSDK' in window) {
        const VKID = window.VKIDSDK;

        VKID.Config.init({
          app: <?= VK_APP_ID ?>,
          redirectUrl: '<?= VK_REDIRECT_URI ?>',
          responseMode: VKID.ConfigResponseMode.Callback,
          source: VKID.ConfigSource.LOWCODE,
          scope: '',
        });

        const oneTap = new VKID.OneTap();

        oneTap.render({
          container: document.getElementById('vk-auth-widget'),
          showAlternativeLogin: true,
          styles: {
            width: 320,
            height: 48,
            borderRadius: 0
          }
        })
        .on(VKID.WidgetEvents.ERROR, vkidOnError)
        .on(VKID.OneTapInternalEvents.LOGIN_SUCCESS, function (payload) {
          const code = payload.code;
          const deviceId = payload.device_id;

          VKID.Auth.exchangeCode(code, deviceId)
            .then(vkidOnSuccess)
            .catch(vkidOnError);
        });

        function vkidOnSuccess(data) {
          console.log('VK ID Success:', data);
          console.log('Full data object:', JSON.stringify(data, null, 2));

          if (data.token && data.user) {
            const params = new URLSearchParams({
              access_token: data.token,
              user_id: data.user.id,
              first_name: data.user.first_name || '',
              last_name: data.user.last_name || '',
              email: data.user.email || '',
              phone: data.user.phone || '',
              photo: data.user.avatar || data.user.photo_200 || data.user.photo_100 || ''
            });

            console.log('Redirecting to callback with params:', params.toString());
            window.location.href = 'vk_callback.php?' + params.toString();
          }
          else if (data.access_token || data.code) {
            console.log('Using fallback format');
            const params = new URLSearchParams();
            if (data.access_token) params.append('access_token', data.access_token);
            if (data.code) params.append('code', data.code);
            if (data.user_id) params.append('user_id', data.user_id);
            if (data.email) params.append('email', data.email);

            window.location.href = 'vk_callback.php?' + params.toString();
          }
          else {
            console.error('Unexpected data format:', data);
            alert('Ошибка: неожиданный формат данных от VK ID.');
          }
        }

        function vkidOnError(error) {
          console.error('VK ID Error:', error);
          alert('Ошибка авторизации через VK ID. Попробуйте еще раз.');
        }
      }
    </script>

    <div class="info-text">
      Нажимая "Войти через ВКонтакте", вы соглашаетесь с использованием данных из вашего профиля ВК.
    </div>

    <div class="back-link">
      <a href="../index.php">Назад на главную</a>
    </div>
  </div>
</body>

</html>
