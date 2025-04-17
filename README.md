Sveiki 

Vēlējos uzrakstīt īsu konspektu te par to kāmdēļ šis viss gāja tik ilgi un sāpīgi..
Oriģināli sākot strādāt Printfulā tika piešķirts macbook, ko tad arī tagad lietoju
arī pēc darba attiecību beigšanas, un te parādījās pirmās lielās problēmas , tieši 
sakarā ar MSsql datu bāzes savienojamību utt.

Ja oriģināli doma bija visu 'dockerizēt'(ar ļoti limitētām zināšanām, jo PF to darīja devops departaments) 
, proti FE un BE un DB katrs savā konteinerā,
tad , pārsteigums - vai nē - uz Mac OS tas nebūt nav tik viegli(vismaz tā man liekās).

Scenārijs 1. FE un BE katrs savā konteinerā , bet nevar piejūgt klāt Mssql(šo scenāriju neturpināju, bet lieki 
pazaudēju laiku un nervus)

Scenārijs 2. - FE un BE visprimitīvākajā formā ar npm install, run dev etc. un Mssql bāzēts uz random custom image, ko 
tad vismaz izdevās piedzīvināt ar docker run -d --name construction-management -p 1433:1433 -e ACCEPT_EULA=Y -e SA_PASSWORD=spudoverlord ngindrek/mssql:arm64

tad kad development ir galā(ja to tā var nosaukt) vēlējos uztaisīt sql dumpu, bet secinājums, ka tādas built in funkcionalitātes
, kas strādā ar mac OS īsti nav(pārbaudīju kādas 4 applikācijas, bet labākais ko varēja dabūt laukā bija bacpac fails 
caur Azure data studio , un tas pats, iespējams, izskatās ka ir corrupted.

Šo visu rakstu, jo galīgi neesmu priecīgs, par to ko esmu te iesniedzis, bet norunāts, bija - tad jāiesniedz.
Honest mistake - vajadzēja pirmajā dienā mest mieru mac OS - mssql compatability versijai un taisīt visu
uz windows, bet nu jau ir par vēlu.

JA gadijumā mans repo vispār tiek caurskatīts, pēc šāda , diezgan drausmīga intro un raudu vēstules,
es varu mēģināt uzcept seederus, kas tad ļautu populēt mssql DB - ko , diemžēl būtu jāprasa jums 
uztaisīt savā galā .... jo sql dumpu tā arī neizdevās iegūt...

JEBkurā gadijumā šeit ir mazs overview , kas te īsti notiek:

/construction-management/api - viss backends - sadalīts pa moduļiem, ar 'api' endpointiem, ko triggero frontends.
/construction-management/frontend - vue + vite + typescript - ļoti maz strādāts ar FE, bet nu ko varēju to uzcepu
/console/controllers - seederis lai dabūtu iekšā sākotnējo useri - php yii database-seeder/create-user  test password new user 3 \console\controllers\DatabaseSeederController::actionCreateUser
    ar ko tad var viegli sataisīt visus pārējos access pointus(caurlaižu api))
    darbiniekus
    būvlaukumus
    veicamos darbus

lai palaistu lokāli : 
backends - php -S localhost:4000 -t api/web
frontends - npm run dev

tākā man diemžēl nav sql dumps pieejams - tad db tabulas etc vajag palaist ar
php yii migrate

ja ir problēmas ar Rbac roles - php yii rbac/init



skumji, bet nu , kā ir - tā ir 

jaukas brīvdienas
