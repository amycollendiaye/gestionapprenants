<style>
    body {
        background-color: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
    }

    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        height: auto;
        font-family: 'Arial', sans-serif;
        background-color: white;
        border-radius: 25px;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        padding: 30px;
        position: relative;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    /* Bordure verte à gauche */
    .error-container::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        border-top-left-radius: 25px;
        border-bottom-left-radius: 25px;
    }

    /* Bordure orange à droite */
    .error-container::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        border-top-right-radius: 25px;
        border-bottom-right-radius: 25px;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    .emoji {
        font-size: 70px;
        margin-bottom: 20px;
        animation: bounce 2s ease-in-out infinite;
    }

    .error-number {
        font-size: 80px;
        font-weight: bold;
        color: #333;
        margin: 0;
        margin-bottom: 10px;
    }

    .error-text {
        font-size: 22px;
        color: #FF6600;
        margin: 0;
        margin-bottom: 15px;
    }

    .error-description {
        font-size: 16px;
        color: #666;
        text-align: center;
        margin: 0 0 30px 0;
        line-height: 1.5;
    }

    .back-button {
        display: inline-block;
        width: 100%;
        padding: 15px;
        font-size: 16px;
        color: white;
        background: #FF6600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.3s;
    }

    .back-button:hover {
        background: #e65c00;
    }
</style>

<div class="error-container">
    <div class="emoji">😭🥺</div>
    <h1 class="error-number">404</h1>
    <h2 class="error-text">Oups! Page non trouvée guenal dou fi</h2>
    <p class="error-description">
        Désolé, la page que vous recherchez semble avoir disparu... 🤔
    </p>
    <a href="?page=login" class="back-button">
        ← Retour à la page de connexion
    </a>
</div>

