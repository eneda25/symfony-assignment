<?php

namespace App\Command;

use Psr\Container\ContainerInterface;use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class hintoTaskCommand extends Command {

    protected $container;
    private const USERS_URL = 'https://jsonplaceholder.typicode.com/users';
    private const POSTS_URL = 'https://jsonplaceholder.typicode.com/posts';
    private const MAX_POST_LIMIT = 3;

    protected static $defaultName = 'app:send-data';
    protected static $defaultDescription = 'Gets data from json files and send them to email';

    public function __construct(ContainerInterface $container, MailerInterface $mailer) {
        $this->container = $container;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {}

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        // json file path for users list
        $usersUrl  = self::USERS_URL;
        //read users json file from url
        $readUsersJSONFile = file_get_contents($usersUrl);
        //convert json to array
        $usersList = json_decode($readUsersJSONFile, TRUE);

        $postsUrl  = self::POSTS_URL;
        //read posts json file from url
        $readPostsJSONFile = file_get_contents($postsUrl);
        $postsList = json_decode($readPostsJSONFile, TRUE);
        $userPosts = [];

        //loop through users list
        foreach ($usersList as $user){
            $userPosts[$user['id']] = [];
            $userPosts[$user['id']]['user_name'] = $user['name'];
            $postNumber = 1;
            //loop through posts list
            foreach ($postsList as $post){
                if ($user['id'] == $post['userId']){
                    $userPosts[$user['id']]['post_title'][] = $post['title'];
                    $postNumber++;
                }
                if ($postNumber > self::MAX_POST_LIMIT) {
                    break;
                }
            }
        }

        //sends an email with the posts written by every user
        $message = (new TemplatedEmail())
            ->from('admin@mailnator.com')
            ->to('test@mailnator.com')
            ->subject('User Posts')
            ->htmlTemplate('email/template.html.twig')
            ->context([
                'posts' => $userPosts,
            ]);

        $this->mailer->send($message);

        $output->writeln('Email was send successfully!');
        return 1;
    }
}
?>
