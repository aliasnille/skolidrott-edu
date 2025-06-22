<?php
// Start a session
session_start();

// Unset course
unset($_SESSION['course']);

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    
    // Redirect to the login page
    header('Location: ' . CONFIG['siteURL']. '/');
    exit();

}

// Page info
$page_title = 'Instruktioner';

include 'templates/header.php';
?>
        <main class="container my-5 fs-5 fw-normal">

            <h1 class="fs-1">Instruktioner för utbildningsplattformen</h1>

            <hr class="mb-5">

            <p class="fs-4 mb-5">
                Utbildningsplattformen är skapad för utbildare och ledare som ska utbilda unga inom olika ämnen, såsom ledarskap. Plattformen fungerar som en samlingsplats för olika utbildningar och ger utbildare tillgång till strukturerat material som de kan använda vid utbildningstillfällen.
            </p>

            <h2>1. Startsida och tillgängliga utbildningar</h2>

            <p>
                När en utbildare loggar in på plattformen möts de av en översikt med följande innehåll.
            </p>

            <ul>
                <li><strong>Tillgängliga utbildningar</strong> - utbildningar som utbildaren kan starta.</li>
                <li><strong>Dina utbildningar</strong> - utbildningar som utbildaren redan har skapat kopior av för att anpassa och använda.</li>
            </ul>

            <p>
                För att starta en utbildning klickar utbildaren på <strong>"Starta"</strong>-knappen.
            </p>

            <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/instruktioner-start.png" class="img-fluid w-50 border mb-5">

            <h2>2. Duplicera en utbildning</h2>

            <p>
                Utbildare har möjlighet att skapa en kopia av en utbildning för att anpassa den. Detta görs enligt nedan.
            </p>

            <ol>
                <li>Klicka på <strong>"Duplicera"</strong> på en utbildning.</li>
                <li>Ett fönster öppnas där utbildaren kan ange en titel för kopian.</li>
                <li>Klicka på <strong>"Duplicera"</strong> för att spara.</li>
            </ol>

            <p>
                Den duplicerade utbildningen visas sedan under <strong>"Dina utbildningar"</strong>.
            </p>

            <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/instruktioner-duplicera.png" class="img-fluid w-50 border mb-5">            

            <h2>3. Redigera en utbildning</h2>

            <p>
                Efter att ha duplicerat en utbildning kan utbildaren anpassa dess innehåll.
            </p>

            <ul>
                <li>Varje utbildning består av olika <strong>slides</strong> (motsvarande PowerPoint-presentationer).</li>
                <li>Utbildaren kan <strong>inkludera eller exkludera</strong> slides genom att använda knappen under varje kort.</li>
                <li>Slides kan <strong>ändra ordning</strong> genom att dra och släppa.</li>
            </ul>

            <p>
                Efter att ha gjort ändringar är det viktigt att klicka på <strong>"Spara"</strong>.
            </p>


            <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/instruktioner-redigera.png" class="img-fluid w-50 border mb-5">                  

            <h2>4. Profil och inställningar</h2>

            <p>
                På profilsidan kan utbildaren hantera sina uppgifter och information enligt nedan.
            </p>

            <ul>
                <li>Ändra sina personuppgifter (namn, kontaktuppgifter).</li>
                <li>Ladda upp en profilbild.</li>
                <li> Skriva en kort beskrivning om sig själv.</li>
                <li>Uppdatera sitt lösenord.</li>
                <li>Svara på enkla "Sant eller falskt"-frågor.</li>
            </ul>

            <p>
                Glöm inte att klicka på <strong>"Spara"</strong> efter att ha gjort ändringar.
            </p>

            <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/instruktioner-profil.png" class="img-fluid w-50 border mb-5">      

            <h2>5. Starta en utbildning</h2>

            <p>
                När utbildaren startar en utbildning kommer en fråga: <strong>"Vem utbildar du med?"</strong>.
            </p>

            <ul>
                <li>Utbildaren kan välja att utbilda ensam eller tillsammans med någon annan.</li>
                <li>Efter att ha valt ett alternativ klickar utbildaren på <strong>"Fortsätt"</strong> för att starta.</li>
            </ul>
            
            <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/instruktioner-utbildare.png" class="img-fluid w-50 border mb-5">      

            <h2>6. Genomföra en utbildning</h2>

            <p>
                Själva utbildningen presenteras som en interaktiv presentation där utbildaren kan göra enligt nedan.
            </p>

            <ul>
                <li><strong>Navigera mellan slides</strong> genom att använda pilarna till vänster och höger.</li>
                <li>Visa information i en tydlig och strukturerad form för deltagarna.</li>
            </ul>

            <p class="mb-4">
                Utbildningen följer en logisk ordning och kan inkludera både text, bilder och interaktiva moment.
            </p>

            <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/instruktioner-presentation.png" class="img-fluid w-50 border mb-5">                  

            <p class="fst-italic">
                Utbildningsplattformen ger utbildare tillgång till strukturerade och interaktiva utbildningar. Den erbjuder möjligheter att anpassa och hantera utbildningar samt att enkelt genomföra dem med deltagare. 
            </p>

        </main>
<?php include 'templates/footer.php'; ?>