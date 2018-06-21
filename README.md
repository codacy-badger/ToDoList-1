<h3>If you want to contribute to this project, read <a title="collaborate" href="https://github.com/gaeldelmer/ToDoList/blob/master/collaborate.md">collaborate.md</a></h3>

<h1>Install Project</h1>

<em>Step 1</em>
<p>Download or clone this repository.</p>
<em>Step 2</em>
<p>In a terminal write "php composer.phar install" and type the parameters when they are requested</p>
<em>Step 3</em>
<p>In the terminal type "bin/console doctrine:database:create" and "bin/console doctrine:schema:update --force" to create the database.</p>
<em>Step optional : fixtures</em>
<p>In the terminal type "bin/console doctrine:fixtures:load" to create two users and ten tasks for example.</p>
<em>Step optional : database test</em>
<p>In the terminal type "bin/console doctrine:database:create --env=test" and "bin/console doctrine:schema:update --force --env=test".</p>
