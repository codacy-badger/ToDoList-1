<h1>Authentication</h1>

<h2>Security.yml<h2>
<p>The authentication information are in app/config/security.yml file.</p>
<h3>Encoders</h3>
<code>AppBundle\Entity\User: bcrypt</code>
<p>Password encryption method</p>
<h3>Providers</h3>
<pre><code>doctrine:
    entity:
        class: AppBundle:User
        property: username
</code></pre>
<p>This is the entity that represents the users, they are stored in the database</p>
<h3>Role_hierarchy</h3>
<pre><code>ROLE_USER:
ROLE_ADMIN: ROLE_USER
</code></pre>
<p>These are the two roles that a user can have. The administrator has the rights of the basic user.</p>
<h3>Firewalls</h3>
<pre><code>main:
   anonymous: ~
   pattern: ^/
   form_login:
       login_path: login
       check_path: login_check
       always_use_default_target_path:  true
       default_target_path:  /
   logout: ~
</code></pre>
<p>There is only one firewall for the site (except for the development one). The login routes are noted here.</p>
<h3>Access_control</h3>
<pre><code>access_control:
        - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/users/create, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/users, roles: ROLE_ADMIN }
        - { path: ^/, roles: ROLE_USER }
</code></pre>
<p>
All routes are only accessible to connected users with three exceptions :
    <ul>
        <li>the login page is accessible to all</li>
        <li>The one to register too</li>
        <li>Pages listing users and user edits are only accessible to admin</li>
    </ul>
</p>

<h2>User entity</h2>
<p>A user has 5 characteristics
    <ul>
        <li>id : the identifiant of the user (unique)</li>
        <li>username : unique</li>
        <li>password : encoded by bcrypt</li>
        <li>email : unique</li>
        <li>role : an array that contains either role_user or role_admin</li>
    </ul>
</p>

<h2>Task controller</h2>
<p>a task can be erased only by the user who created it or by an administrator if it is anonymous. This check is located the src/AppBundle/Controller/TaskController.php.</p>
<pre><code>
    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {
        $em = $this->getDoctrine()->getManager();

        $author = $task->getAuthor();

        if ($author == null) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $em->remove($task);
                $em->flush();

                $this->addFlash('success', 'La tâche a bien été supprimée.');
            } else {
                $this->addFlash('error', 'Vous devez être administrateur pour supprimer une tâche anonyme.');
            }
        } else {
            if ($author == $this->get('security.token_storage')->getToken()->getUser()) {
                $em->remove($task);
                $em->flush();

                $this->addFlash('success', 'La tâche a bien été supprimée.');
            } else {
                $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres tâches.');
            }
        }

        return $this->redirectToRoute('task_list');
    }
</code></pre>