## Running the Project

1. Install XAMPP and start **Apache** and **MySQL**.'

2. Clone the project into the XAMPP `htdocs` folder so the path is `htdocs/bedlink`.

3. Copy `config/.env.example` to `config/.env`.

4. In phpMyAdmin, create a database called `bedlink`, then import `database/schema.sql` and `database/seed.sql`.

5. Visit `http://localhost/bedlink/` and log in with a test account (password `Test1234!`).