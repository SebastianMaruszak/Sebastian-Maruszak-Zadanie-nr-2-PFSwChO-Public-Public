# Kanban Board (LAMP on Minikube)

Prosta aplikacja **Tablica Kanban** zbudowana w stacku **LAMP** (Linux, Apache, MySQL, PHP), uruchamiana na **Minikube / Kubernetes**.  
Umożliwia tworzenie zadań i przesuwanie ich między kolumnami **To Do**, **In Progress** i **Done**.



## 🏗️ Technologie

- **PHP 8.x** (backend)
- **Apache** (web server)
- **MySQL 8.x** (baza danych)
- **Minikube + Kubernetes** (lokalny klaster)
- **ConfigMap** (inicjalizacja bazy danych i tabel)
- HTML/CSS (frontend)



## 📁 Struktura projektu

```
kanban-app/
├── k8s/
│   ├── mysql-deployment.yaml
│   ├── mysql-service.yaml
│   ├── mysql-secret.yaml
│   ├── apache-deployment.yaml
│   ├── mysql-init.sql
│   ├── apache-service.yaml
│   ├── apache-ingress.yaml
│   └── mysql-init-configmap.yaml
├── app/
│   ├── index.php
│   ├── board.php
│   ├── delete_task.php
│   ├── add_task.php
│   ├── move_task.php
│   └── db.php
├── Dockerfile
└── README.md
```


## ⚡ Funkcjonalności

- Wyświetlanie zadań w kolumnach Kanban (`To Do`, `In Progress`, `Done`)
- Dodawanie nowych zadań
- Zmiana statusu zadania (przesuwanie między kolumnami)
- Automatyczne tworzenie bazy danych i tabel przy pierwszym uruchomieniu MySQL



## 🛠️ Instalacja / Uruchomienie

### 1️⃣ Uruchomienie Minikube
```bash
minikube start
```
### 2️⃣ Zbudowanie obrazu PHP + Apache w Minikube
```bash
minikube -p minikube docker-env --shell powershell | Invoke-Expression
docker build -t kanban-app:latest .
```
### 3️⃣ Utworzenie ConfigMap dla inicjalizacji MySQL
```bash
kubectl apply -f k8s/mysql-init-configmap.yaml
```
### 4️⃣ Utworzenie Secretu z hasłem do MySQL

```bash
kubectl apply -f k8s/mysql-secret.yaml
```
### 5️⃣ Utworzenie Deployment i Service MySQL
```bash
kubectl apply -f k8s/mysql-deployment.yaml
kubectl apply -f k8s/mysql-service.yaml
```
### 6️⃣ Utworzenie Deployment i Service Apache
```bash
kubectl apply -f k8s/apache-deployment.yaml
kubectl apply -f k8s/apache-service.yaml
```
### 7️⃣ Sprawdzenie statusu podów
```bash
kubectl get pods
```
### 8️⃣ Test aplikacji w przeglądarce
```bash
minikube service apache
```

![Kanban 1.0](https://private-user-images.githubusercontent.com/34513330/539255783-4ea2c58c-826a-496e-a068-d07f1b383803.png?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3NjkwOTk0NTAsIm5iZiI6MTc2OTA5OTE1MCwicGF0aCI6Ii8zNDUxMzMzMC81MzkyNTU3ODMtNGVhMmM1OGMtODI2YS00OTZlLWEwNjgtZDA3ZjFiMzgzODAzLnBuZz9YLUFtei1BbGdvcml0aG09QVdTNC1ITUFDLVNIQTI1NiZYLUFtei1DcmVkZW50aWFsPUFLSUFWQ09EWUxTQTUzUFFLNFpBJTJGMjAyNjAxMjIlMkZ1cy1lYXN0LTElMkZzMyUyRmF3czRfcmVxdWVzdCZYLUFtei1EYXRlPTIwMjYwMTIyVDE2MjU1MFomWC1BbXotRXhwaXJlcz0zMDAmWC1BbXotU2lnbmF0dXJlPWYxZTE4YmJkNWVjYmVjMjhiZDkyYmMzYTQ2YjEzOGRlZmZjNjBmNGM0NzNmODhlYWVhOGZmZTU3NGY2NGI0OTUmWC1BbXotU2lnbmVkSGVhZGVycz1ob3N0In0.8M5Vl9PHH5KfindXW-4i0hRUiIEMd-VoDob5rfxN1tU)


<br/><br/>

# 🔧 Konfiguracja bazy danych

- Host: `mysql` (nazwa Service)  
- Baza danych: `kanban`  
- Użytkownik: `kanban_user`  
- Hasło: `kanban_pass`  

MySQL automatycznie tworzy bazę i tabelę `tasks` przy pierwszym starcie.

#### 💡 Debug / troubleshooting

- `1045 Access denied` → Należy usunąć stare pod’y / wolumeny MySQL i uruchomić ponownie  
- `2002 Can't connect` → zawsze podać host Service - `-h mysql`  


### Sprawdzić logi:
```bash
kubectl logs deployment/mysql
kubectl logs deployment/apache
```

### Test PDO w kontenerze Apache:
```bash
kubectl exec -it deployment/apache -- php -r '
$pdo = new PDO("mysql:host=mysql;dbname=kanban","kanban_user","kanban_pass");
echo "OK\n";'
```
<br/>
<br/>

# 🌐 Routing aplikacji przez Ingress

Ingress pozwala w Kubernetes wystawić aplikację HTTP/HTTPS na zewnątrz klastra, definiując reguły routingu.

### 1️⃣ Włączyć Ingress w Minikube
```bash
minikube addons enable ingress
kubectl get pods -n kube-system
```
### 2️⃣ Dodać wpis w `/etc/hosts`
```
<minikube_ip> brilliantapp.zad
```
### 4️⃣ Zastosowanie Ingress
```bash
kubectl apply -f k8s/apache-ingress.yaml
kubectl get ingress
```
### 5️⃣ Aplikacja dostępna jest od teraz pod linkiem:

http://brilliantapp.zad

<br/>

✅ Powinna wyświetlić się tablica Kanban.

### 6️⃣ Diagram routingu z Ingress

```
User Browser
      |
      v
 ┌────────────────────┐
 │  brilliantapp.zad  │
 └────────────────────┘
      |
      v
 ┌──────────────────────────┐
 │ NGINX Ingress Controller │
 └──────────────────────────┘
      |
      v
 ┌────────────────┐
 │ Apache Service │
 └────────────────┘
      |
      v
 ┌────────────────┐
 │ MySQL Service  │
 └────────────────┘

```
<br/><br/><br/><br/>


# 🔝 CZĘŚĆ NIEOBOWIĄZKOWA

<br/>

## 🔄 Aktualizacja aplikacji Kanban – opis i weryfikacja

### 1️⃣ Krótki opis zmian w aplikacji (widocznych po aktualizacji)

W ramach aktualizacji aplikacji Kanban (Apache + PHP) wprowadzono następujące zmiany funkcjonalne i wizualne:

- Dodano informację o wersji aplikacji w ciele strony:
  **„Kanban Board 2.0 UPDATE”**

![Kanban 2.0](https://private-user-images.githubusercontent.com/34513330/539255784-9d08609c-d351-4652-af6f-13ccb854f020.png?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3NjkwOTk0NTAsIm5iZiI6MTc2OTA5OTE1MCwicGF0aCI6Ii8zNDUxMzMzMC81MzkyNTU3ODQtOWQwODYwOWMtZDM1MS00NjUyLWFmNmYtMTNjY2I4NTRmMDIwLnBuZz9YLUFtei1BbGdvcml0aG09QVdTNC1ITUFDLVNIQTI1NiZYLUFtei1DcmVkZW50aWFsPUFLSUFWQ09EWUxTQTUzUFFLNFpBJTJGMjAyNjAxMjIlMkZ1cy1lYXN0LTElMkZzMyUyRmF3czRfcmVxdWVzdCZYLUFtei1EYXRlPTIwMjYwMTIyVDE2MjU1MFomWC1BbXotRXhwaXJlcz0zMDAmWC1BbXotU2lnbmF0dXJlPTM5OWNlNjNmMWI1YWI2YjIxOGQ0M2ZlNzk4NWZmMjA3NjU4N2VkMTMwYTU5YzFkMDBlMWM1ZjgwNWY1NDM3OTkmWC1BbXotU2lnbmVkSGVhZGVycz1ob3N0In0.V4msMpkSoXfa0AT78-ZmdNTZ687wNvZwM8Ec6Sq15Yw)

Zmiana umożliwia jednoznaczną weryfikację, że nowa wersja aplikacji została poprawnie wdrożona.

---

### 2️⃣ Zmiany w plikach konfiguracyjnych

W celu przeprowadzenia aktualizacji bez przerywania działania aplikacji nie było konieczne wprowadzanie zmian
w konfiguracji bazy danych ani w plikach Ingress lub Service.

Jedyną zmianą konfiguracyjną była aktualizacja obrazu kontenera w pliku Deployment:

Plik: `k8s/apache-deployment.yaml`
```yaml
spec:
  replicas: 2
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
```
```yaml
image: kanban-app:2.0
```
---

### 3️⃣ Ilustracja procesu aktualizacji i testów poprawności
- Krok 1: Zbudowanie nowej wersji obrazu aplikacji:
```bash
docker build -t kanban-app:2.0 .
```
- Krok 2: Aktualizacja Deployment bez przestoju:
```bash
kubectl set image deployment/apache apache=kanban-app:2.0
```
- Krok 3: Monitorowanie procesu Rolling Update
```bash
kubectl rollout status deployment apache
kubectl get pods
```
```
NAME                      READY   STATUS    RESTARTS   AGE
...
apache-656d669668-28djd   1/1     Running   0          7s
```

Podczas aktualizacji:

* Nowy Pod uruchamiany jest równolegle do starego

* Stary Pod usuwany jest dopiero po osiągnięciu stanu READY przez nowy

* Aplikacja pozostaje cały czas dostępna
---
### 4️⃣ Test poprawności działania aplikacji
```bash
minikube service apache
```

lub pod linkiem:<br/>

http://brilliantapp.zad

---

### 5️⃣ Weryfikacja aktualizacji
- Strona ładuje się bez przerwy podczas wdrożenia

- Widoczny jest nowy numer wersji aplikacji

- Wszystkie funkcje Kanban (dodawanie, usuwanie i przenoszenie zadań) działają poprawnie

- Brak błędów HTTP oraz błędów połączenia z bazą danych