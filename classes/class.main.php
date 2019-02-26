<?php
class main
{
  private function getWebPage( $url )
  {
    $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(
      CURLOPT_CUSTOMREQUEST  =>"GET",
      CURLOPT_POST           =>false,
      CURLOPT_USERAGENT      => $user_agent,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER         => false,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_ENCODING       => "",
      CURLOPT_AUTOREFERER    => true,
      CURLOPT_CONNECTTIMEOUT => 120,
      CURLOPT_TIMEOUT        => 120,
      CURLOPT_MAXREDIRS      => 10,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false
    );

    $ch = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err = curl_errno( $ch );
    $errmsg = curl_error( $ch );
    $header = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    return $content;
  }

  private function setUrl()
  {
    $this->id = intval($this->id);
    $urls = array(
      1 => 'https://www.elgordo.com/results/euromillonariaen.asp',
      2 => 'http://www.lotto.pl/lotto/wyniki-i-wygrane',
      3 => 'http://www.lotto.pl/eurojackpot/wyniki-i-wygrane'
    );
    $this->url = $urls[$this->id];
  }

  private function setUrlData()
  {
    if (empty($this->url)) return;

    require 'classes/simple_html_dom.php';

    $html = new simple_html_dom();
    $html->load(self::getWebPage($this->url));

    $this->pageData = array();

    switch ($this->id)
    {
      case 1:
        foreach($html->find('#center div .result_big .center .body_game .c') as $e) { $this->pageData['date'][$this->id] = $e->innertext; break; }

        foreach($html->find('#center div .result_big .center .body_game .balls .num .int-num') as $e) {
          $this->pageData['numbers'][$this->id][] = $e->innertext;
        }
        foreach($html->find('#center div .result_big .center .body_game .balls .esp .int-num') as $e) {
          $this->pageData['numbers'][$this->id][] = $e->innertext;
        }
      break;

      case 2:
        $no = 0;
        foreach($html->find('body div.gamesPages section div div div .row .col-xl-9. div .page_repeat table tbody tr.wynik') as $e)
        {
          foreach($e->find('td') as $key => $a)
          {
            switch($key)
            {
              case 0:
                $this->pageData['name'][$this->id][$no] = $a->children[0]->attr['alt'];
              break;
              case 1: $this->pageData['number'][$this->id][$no] = $a->innertext; break;
              case 2: $this->pageData['date'][$this->id][$no] = $a->innertext; break;
              case 3:
                foreach($a->find('.sortrosnaco .resultnumber .number span') as $c) $this->pageData['numbers'][$this->id][$no][] = $c->innertext;
              break;
            }
          }
          $no++;
        }
      break;

      case 3:
        $no = 0;
        foreach($html->find('body div.gamesPages section div div div .row .col-xl-9 div .page_repeat table tbody tr.wynik') as $e)
        {
          foreach($e->find('td') as $key => $a)
          {
            switch($key)
            {
              case 0: $this->pageData['number'][$this->id][$no] = $a->innertext; break;
              case 1: $this->pageData['date'][$this->id][$no] = $a->innertext; break;
              case 2:
                foreach($a->find('.sortrosnaco .resultnumber .number span') as $c) $this->pageData['numbers'][$this->id][$no][] = $c->innertext;
                unset($this->pageData['numbers'][$this->id][$no][5]);
                sort($this->pageData['numbers'][$this->id][$no]);
              break;
            }
          }
          $no++;
        }
      break;
    }
  }

  private function saveFile()
  {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="filename.json";');
    echo json_encode($this->pageData);
    exit;
  }

  public function createFile($id=1)
  {
    $this->id = $id;
    $this->setUrl();
    $this->setUrlData();
    $this->saveFile();
  }

}

?>
