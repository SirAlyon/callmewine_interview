# DOMANDA 1: Spiegazione del Progetto Laravel

## Struttura delle Cartelle

- **app/Http/Controllers**: Contiene i controller che gestiscono le richieste HTTP.
- **app/Models**: Contiene il modello Cryptocurrency (in questo caso, per semplicità, ho utilizzato un modello 'nonEloquent', non avendo la necessità di salvarli in un db).
- **app/Services**: Contiene la logica di business che non si adatta bene ai controller o ai modelli.
- **app/Repositories**: Contiene il repository che gestisce la logica di accesso ai dati.
- **app/Providers**: Contiene il service provider che è responsabile di registrare i servizi nel container di Laravel.
- **routes/**: Contiene i file di rotte `web.php` per definire le rotte dell'applicazione.
- **resources/views**: Contiene i file delle viste Blade.

## Design Pattern Utilizzati

### Repository Pattern

**Descrizione**: Astrazione che separa la logica di accesso ai dati dal resto dell'applicazione. Facilita il testing e la manutenibilità del codice.

**Utilizzo**: Implementato per la gestione delle criptovalute, con repository che interagisce con Redis.

---
### Service Pattern

**Descrizione**: Contiene la logica di business che può essere utilizzata da diversi controller. Mantiene i controller snelli e focalizzati.

**Utilizzo**: Utilizzato per la logica di business relativa al recupero e alla gestione delle criptovalute.

---
### Dependency Injection

**Descrizione**: Tecnica per fornire le dipendenze agli oggetti anziché crearle internamente. Facilita il testing e la modularità.

**Utilizzo**: Implementata attraverso i costruttori dei controller e dei servizi, permettendo l'iniezione delle dipendenze.

---
### Singleton per i Client di Predis e Guzzle

**Descrizione**: Il pattern Singleton garantisce che una classe abbia una sola istanza e fornisce un punto di accesso globale a tale istanza.

**Utilizzo**: Utilizzato per creare una singola istanza di PredisClient e GuzzleClient, migliorando l'efficienza e riducendo l'overhead di creazione di nuove istanze.

## Funzionalità del Progetto

### Recupero Dati da API Esterni

**Descrizione**: Recupera le criptovalute dall'API di CoinMarketCap.

**Implementazione**: Utilizza GuzzleHttp per fare richieste HTTP e recuperare i dati.

### Caching con Redis

**Descrizione**: Memorizza le criptovalute recuperate in Redis per migliorare le prestazioni e garantire la disponibilità dei dati in caso di fallimento dell'API.

**Implementazione**: Utilizza Predis per interagire con Redis, con un repository dedicato per la gestione delle criptovalute. Per semplicità i dati permangono su redis per 1h, ovviamente questa tempistica può essere modificata a dovere in base alle esigenze del progetto.

!!! E' possibile commentare CMC_API_KEY dal file .env per verificare le funzionalità di caching e per la gestione di errori da parte dell'API. !!!

### Gestione delle Viste e Allert

**Descrizione**: Visualizza in una tabella i dati ricevuti, mostra un allert all'utente per indicare se i dati provengono dall'API o dalla cache di Redis.

**Implementazione**: I controller passano i dati alle viste, che li visualizzano dinamicamente.

---

## DOMANDA 2: Modalità Asincrona

### Vantaggi e Svantaggi

Utilizzare la modalità asincrona in un progetto Laravel offre diversi vantaggi. In termini di performance e scalabilità, le richieste dell'utente non devono attendere il completamento delle chiamate API, migliorando così la reattività e l'esperienza utente. Inoltre, delegando le operazioni intensive ai processi di background, si riduce il carico sui server web. La modalità asincrona aumenta anche l'affidabilità del sistema, poiché le operazioni possono essere riprovate in caso di fallimento. Un ulteriore vantaggio è la possibilità di eseguire operazioni di background in parallelo, sfruttando meglio le risorse del server.

Tuttavia, l'adozione della modalità asincrona aggiunge complessità al progetto. La gestione di code, worker e possibili stati di errore richiede una configurazione e un monitoraggio accurato. Inoltre, il debug delle operazioni asincrone può essere più complesso rispetto a quelle sincrone. Un altro svantaggio è rappresentato dalla latenza: i dati aggiornati potrebbero non essere immediatamente disponibili.

### Funzionalità di Laravel

Laravel offre diverse funzionalità per implementare la modalità asincrona.

#### Queues

Il sistema di code di Laravel è robusto e permette di eseguire operazioni in background utilizzando vari driver di coda, come database, Redis e altri. Le operazioni da eseguire in background vengono definite come job, che possono essere creati con il comando `php artisan make:job JobName`. I job nelle code vengono processati dai worker, che possono essere avviati con il comando `php artisan queue:work`.

#### Eventi e Listener

Laravel fornisce un sistema di eventi e listener che può essere utilizzato per gestire operazioni asincrone. Gli eventi rappresentano azioni o cambiamenti di stato che avvengono all'interno dell'applicazione, mentre i listener sono responsabili di rispondere a tali eventi ed eseguire le operazioni necessarie.

#### Task Scheduling

Laravel include un sistema di pianificazione dei task che può essere utilizzato per eseguire comandi periodici. Questo è utile per operazioni che devono essere eseguite regolarmente.

#### Web Sockets

Utilizzando Laravel Echo e un server WebSocket, è possibile trasmettere eventi dal backend ai client connessi in modo istantaneo.

## Modalità Sincrona

### Vantaggi e Svantaggi

La modalità sincrona è generalmente più semplice da implementare e mantenere, poiché tutto avviene nel contesto di una singola richiesta. Inoltre, i dati sono immediatamente disponibili e aggiornati. D'altro canto, la modalità sincrona presenta svantaggi significativi in termini di performance. Le richieste dell'utente devono attendere il completamento delle chiamate API, aumentando così il tempo di risposta. Questo può portare a timeout se le chiamate API sono lente o in caso di carico elevato. Scalare un sistema che utilizza solo la modalità sincrona può diventare difficile con un elevato numero di richieste.

### Funzionalità di Laravel

Per quanto riguarda le funzionalità di Laravel, il framework offre un HTTP client basato su Guzzle per fare richieste HTTP in modo sincrono.

---

## Conclusione

La scelta tra modalità sincrona e asincrona dipende dalle esigenze specifiche del progetto. La modalità asincrona è ideale quando si desidera una migliore performance e scalabilità, nonché una maggiore affidabilità, ma a costo di una maggiore complessità. La modalità sincrona è preferibile per progetti più semplici che richiedono coerenza immediata dei dati e dove la semplicità di implementazione e sviluppo è una priorità.

---

## DOMANDA 3 : Invio Massivo di Mail

Per inviare una mail ogni mattina a tutti gli utenti del progetto con l’elenco delle prime 20 criptovalute ordinate per volume delle ultime 24 ore, considerando il limite di invio di 10 email ogni 20 secondi, è necessario utilizzare le code di Laravel con i job e i worker. Inoltre, l'utilizzo di Supervisor su Linux per gestire il riavvio automatico dei worker garantisce che il processo sia affidabile e resistente agli errori.

### Creazione del Job

Per creare un Job con Laravel, si può eseguire il comando:

```
php artisan make:job SendCryptoReportEmail
```


Nel file SendCryptoReportEmail.php, definire la logica per inviare l'email. Ipotizzando di avere già una classe di tipo Mailable:

```
class CryptoReport extends Mailable
{
    use Queueable, SerializesModels;

    protected $cryptoData;

    public function __construct($cryptoData)
    {
        $this->cryptoData = $cryptoData;
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.crypto-daily',
            with: [
                'cryptoData' => $this->cryptoData,
            ],
        );
    }
}
```

Implementare la logica del Job per l'invio delle mail:

```
class SendCryptoReportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        $cryptoData = $this->getCryptoData();
        Mail::to($this->user->email)->send(new CryptoReport($cryptoData));
    }

    protected function getCryptoData()
    {
        //Il metodo per ottenere i dati...
    }
}
```

### Creazione del Comando

La creazione di un comando Laravel risulta particolarmente adatta, esso si occupa di eseguire le query e dispatch dei job per l'invio delle email.

```
php artisan make:command SendDailyCryptoReport
```
```
class SendDailyCryptoReport extends Command
{
    protected $signature = 'send:daily-crypto-report';
    protected $description = 'Invia un report giornaliero delle prime 20 criptovalute per volume agli utenti';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::all();
        foreach ($users as $index => $user) {
            SendCryptoReportEmail::dispatch($user)->delay(now()->addSeconds(20 * intdiv($index, 10)));
        }
        $this->info('Report giornaliero inviato a tutti gli utenti.');
    }
}
```

### Pianificazione del Comando

Pianificare il comando per l'invio delle email ogni mattina nel metodo schedule del file app/Console/Kernel.php.

```
protected function schedule(Schedule $schedule)
{
    $schedule->command('send:daily-crypto-report')->dailyAt('08:00');
}
```

### Avvio dei Worker di Laravel

Avviare i worker di Laravel con il comando:

`php artisan queue:work`

### Utilizzo di Supervisor su Linux

Utilizzare Supervisor per gestire i worker di Laravel offre numerosi vantaggi. Supervisor monitora i processi di background e garantisce che vengano riavviati automaticamente in caso di fallimento, aumentando la resilienza del sistema. Questo è particolarmente utile in ambienti di produzione dove è essenziale mantenere la disponibilità e l'affidabilità dei servizi. Inoltre, Supervisor consente di gestire più processi simultaneamente, migliorando la capacità di elaborazione delle code di Laravel e assicurando che i job vengano processati in modo tempestivo.

(https://laravel.com/docs/11.x/queues#starting-supervisor)

### Conclusione

In conclusione, l'uso di Laravel Queue con job e worker, combinato con la gestione dei processi tramite Supervisor, permette di realizzare una funzionalità di invio email scalabile ed efficiente, rispettando i limiti di invio e garantendo la consegna affidabile delle email agli utenti.

---

## DOMANDA 4: Salvataggio dei Dati sul Database ad Ogni Richiesta

Per salvare i dati sul database ad ogni richiesta in un'applicazione Laravel, è necessario seguire una serie di passaggi per assicurarsi che i dati vengano gestiti correttamente e in modo efficiente. Questo processo può essere suddiviso in diversi passi chiave: configurazione del database, creazione dei modelli, definizione delle migrazioni, creazione dei controller, e gestione delle richieste e risposte. Di seguito sono riportati i dettagli di ogni passaggio.

### Configurazione del Database

Assicurarsi che il database sia correttamente configurato nel file .env della tua applicazione Laravel. Questo include la definizione delle credenziali del database e delle informazioni di connessione.

Per esempio:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Creazione dei Modelli

Creare un modello Eloquent che rappresenti la tabella del database in cui verranno salvati i dati. 

```
php artisan make:model Cryptocurrency
```

### Definizione delle Migrazioni

Creare una migrazione per definire la struttura della tabella nel database. Utilizzare il comando artisan per generare la migrazione.

```
php artisan make:migration create_cryptocurrencies_table
```
```
class CreateCryptocurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('cryptocurrencies', function (Blueprint $table) {
            $table->id();
            $table->string('field1');
            //tutti le colonne...
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cryptocurrencies');
    }
}
```

### Creazione del Controller
Creare un controller che gestisca le richieste e salvi i dati nel database. Utilizzare il comando artisan per generare il controller.

```
class CryptocurrencyController extends Controller
{
    protected $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    public function fetchAndStoreCryptocurrencies()
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        $parameters = [
            'start' => '1', // Inizia dalla prima criptovaluta
            'limit' => '100', // Limita il risultato a 100 criptovalute
            'convert' => 'USD' // Converti i prezzi in USD
        ];

        $headers = [
            'Accept' => 'application/json',
            'X-CMC_PRO_API_KEY' => env('CMC_API_KEY') // Usa l'API key dal file .env
        ];

        try {
            // Richiesta HTTP per ottenere i dati delle criptovalute
            $response = $this->guzzleClient->request('GET', $url, [
                'headers' => $headers,
                'query' => $parameters
            ]);

            // Decodifica la risposta JSON in un array associativo
            $data = json_decode($response->getBody(), true);


            // Itera attraverso i dati delle criptovalute e salva nel database
            foreach ($data['data'] as $cryptoData) {
                //All'occorrenza utilizzare metodi come updateOrCreate etc.
                Cryptocurrency::create([
                    'id' => $cryptoData['id'],
                    'rank' => $cryptoData['cmc_rank'],
                    'name' => $cryptoData['name'],
                    'symbol' => $cryptoData['symbol'],
                    'price' => $cryptoData['quote']['USD']['price'],
                    'market_cap' => $cryptoData['quote']['USD']['market_cap']
                ]);
            }

            return response()->json(['message' => 'Cryptocurrencies data fetched and stored successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch data from API'], 500);
        }
    }
}
```

### Conclusione

Seguendo questi passaggi, è possibile garantire che i dati vengano salvati in modo sicuro ed efficiente nel database.



## DOMANDA 5: Limitare l'Accesso alla Pagina agli Utenti Italiani

Per limitare l'accesso a una pagina specifica solo agli utenti che si collegano dall’Italia, si possono utilizzare diversi approcci, sia con Laravel che con PHP in generale.

### Utilizzo di Middleware con Geolocalizzazione

Utilizzando un pacchetto di Geolocalizzazione come: https://github.com/stevebauman/location è possibile gestire la logica all'interno del middleware:

```
class CheckCountry
{
    public function handle(Request $request, Closure $next)
    {
        $location = Location::get($request->ip());

        if ($location && $location->countryCode === 'IT') {
            return $next($request);
        }

        return response()->json(['error' => 'Accesso non autorizzato.'], 403);
    }
}
```

Per poi associarlo alla rotta da limitare:

Route::get('/', [CryptocurrencyController::class, 'index'])->middleware(CheckCountry::class);

### Utilizzo delle Variabili SERVER di PHP

In alternativa, è possibile utilizzare le variabili $_SERVER di PHP per ottenere l'indirizzo IP dell'utente e determinare la sua localizzazione.

```
class CheckCountryPHP
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $locationData = geoip($ip); // Usa un servizio di geolocalizzazione IP

        if ($locationData && $locationData['country'] === 'Italy') {
            return $next($request);
        }

        return response()->json(['error' => 'Accesso non autorizzato.'], 403);
    }
}
```

### Conclusione

Utilizzando un middleware, è possibile limitare l'accesso a una pagina specifica agli utenti italiani. 