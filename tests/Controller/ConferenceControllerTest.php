<?php

namespace App\Tests\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Give your feedback');
    }

    
    public function testCommentSubmission()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/conference/amsterdam-2019');
        $client->submitForm('Submit', [
            'comment[author]' => 'Fabien',
            'comment[text]' => 'Some feedback from an automated functional test',
            'comment[email]' => $email = 'me@automat.ed',
            'comment[photo]' => dirname(__DIR__, 2).'/public/images/under-construction.gif',
        ]);
        $this->assertResponseRedirects();

        // simulate comment validation
        $comment = self::getContainer()->get(CommentRepository::class)->findOneByEmail($email);
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();

        $value = $crawler->filter('div')->text();
        print "\n\n $value";

        $this->assertSelectorExists('div:contains("There are ")');
    }

    
    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('View');

       
        #$value = $crawler->filter('h2')->text();
        #$value2 = $crawler->filter('div')->text();
        #print "\n\n $value"; #Give your feedback! -> Seitenwechsel hat nicht stattgefunden für $crawler
        #print "\n $value2"; #Give your feedback! -> Seitenwechsel hat nicht stattgefunden für $crawler

        $this->assertPageTitleContains('Amsterdam');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Amsterdam 2019');
        #$this->assertSelectorExists('div:contains("There are 1 comments")');
       
       
    }
    
}