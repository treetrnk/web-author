<?php 
  
class Page {

  // Constructor function //////////////////////////////////////////////////////
  public function __construct($id="", $search=false) {

    if (empty($id)) {
      $path = explode('?', $_SERVER['REQUEST_URI']);

      $path[0] = substr($path[0], -1) != '/' ? $path[0] . '/' : $path[0];

      if ($path[0] == '/' || empty($path[0]) || $path[0] == '/home/') {
        $id = config('home-id');
      } else if (strtolower($path[0]) == "/search/") {
        $search = true;
        $this->setSearch();
      } else {
        $post = getPostByLocation($path[0]);
        $id = !empty($post) ? $post['id'] : config('404-id'); 
      }
    }

    if (!$search) {

      $this->id = $this->validateUnpublished($id);
      $post = $GLOBALS['allposts']["$this->id"];
      
      foreach($post as $key => $detail) {
        $this->{$key} = $detail;
      }

      $PD = new Parsedown();
      $this->formattedBody = $PD->text($this->body);
      $this->description = substr(strip_tags($this->formattedBody), 0, 150) . "...";
      $this->date = (!empty($this->time)) ? date_format(date_create($this->time), "M. j, Y - g:i A") : "";
      $this->parentDetails = ($this->parent != 0) ? $GLOBALS['allposts']["$this->parent"] : "";
      $this->breadcrumbs = ["<li class='active'>$this->title</li>"];
      $this->setParents();
      $this->setBanner();
      $this->section = !empty($parents) ? $parents[0]["title"] : "";
      $this->url = "";
      $this->search = false;
      $this->children = getChildren($this->id);
      $this->setWords();
			$this->total_word_count();
      $this->setBooktitle();
      $this->setSidebar();
    }
  }

  // Validate viewing of unpublished page //////////////////////////////////////
  public function validateUnpublished ($id) {
    if (empty($GLOBALS['allposts']["$id"])) { // Check if exists
      return config('404-id');
    }

    $page = $GLOBALS['allposts']["$id"]; // Check preview code
    if (empty($page["time"]) || strtotime($page["time"]) > strtotime(date("Y-m-d H:i:s"))) { //if needs preview
      if (empty($_GET['preview']) || !password_verify($page["title"].config('obfuscate'), urldecode($_GET['preview']))) { //if fail
        return config('404-id');
      }
    }
    return $id;
  }

  // Set parent details ////////////////////////////////////////////////////////
  public function setParents() {
    $this->parents = [];
    if ($this->parent != 0) {
      $crntParent = $this->parentDetails;

      while (!empty($crntParent)) {
        $this->parents[] = $crntParent;
        array_unshift($this->breadcrumbs, "<li><a href='$crntParent[location]'>$crntParent[title]</a></li>");
        if ($crntParent['parent'] != 0) {
          $crntParent = $GLOBALS['allposts']["$crntParent[parent]"];
        } else {
          $crntParent = NULL;
        }
      }
    }
  }

  // Set banner image //////////////////////////////////////////////////////////
  public function setBanner() {
    if (empty($this->banner)) {
      foreach ($this->parents as $p) {
        if (!empty($p["banner"])) {
          $this->banner = $p["banner"];
          break;
        }
      }
    }

    if (empty($this->banner)) {
      $this->banner = config('banner');
    }
  }

  // Set word count and read time //////////////////////////////////////////////
  public function setWords() {
    $this->words = str_word_count(strip_tags($this->formattedBody),0,"&#039;");
    $this->readTime = "< 1 min.";
    if ($this->words > 224) {
      $slowRead = round($this->words/150);      
      $fastRead = round($this->words/200);      
      $this->readTime = "$fastRead - $slowRead mins.";
    }
  }

	// Get total word count of story /////////////////////////////////////////////
	public function total_word_count() {
		$wordcount = 0;
		$children = $this->children;
		foreach(mysqli_fetch_assoc($children) as $child) {	
			$c = new Page($child["id"]);
			$wordcount += $c->words;
		}
		$this->total_words = $wordcount;
	}

	// Get total read time ///////////////////////////////////////////////////////
	public function total_read_time() {
		return round($this->total_words/200) . " - " . round($this->total_words/150) . " mins.";
	}

  // Set booktitle and title prefix ////////////////////////////////////////////
  public function setBooktitle() {
    $this->booktitle = "";
    $this->titlePrefix = "";
    if (($this->type == 'chapter' || $this->type == 'post') && !empty($this->parentDetails)) {
      $title = $this->parentDetails['title'];
      $this->booktitle = "<h1>$title</h1>";
      $this->titlePrefix = "$title: ";
    } elseif ($this->type == 'story') {
      $this->booktitle = "<h1>$this->title</h1>";
    }
  }

  // Set page sidebar //////////////////////////////////////////////////////////
  public function setSidebar() {
    $PD = new Parsedown();
    if (!empty($this->sidebar)) {
      $this->sidebar = $PD->text($this->sidebar);
    } elseif (!empty($this->parentDetails) && ($this->type == 'chapter' || $this->type == 'post')) {
      $this->sidebar = $PD->text($this->parentDetails['sidebar']);
    } else {
      $this->sidebar = "";
    }
  }

  // Get Next Chapter //////////////////////////////////////////////////////////
  public function NextPrev () {
    $nextsql = "SELECT id,title,location FROM posts
      WHERE parent = $this->parent
        AND time < CURRENT_TIMESTAMP
        AND (sort > $this->sort OR time > '$this->time')
        AND id != $this->id
      ORDER BY sort ASC, time ASC
      LIMIT 1";
    $nextChapter = "";
    $nextChapLi = "<li class='next disabled'><a href='#'><small>Next <span aria-hidden='true'>&rarr;</span></small></a></li>";
    if ($nextresult = mysqli_query(dbConnect(), $nextsql)) {
      $nextChapter = mysqli_fetch_array($nextresult);
      if (!empty($nextChapter)) {
        $nextChapLi = "<li class='next'><a href='$nextChapter[location]' id='nextPage' rel='next'><small>Next <span aria-hidden='true'>&rarr;</span></small></a></li>";
      }
    }
    // Previous Chapter
    $prevsql = "SELECT id,title,location FROM posts
      WHERE parent = $this->parent
        AND time < CURRENT_TIMESTAMP
        AND (sort < $this->sort OR time < '$this->time')
        AND id != $this->id
      ORDER BY sort DESC, time DESC
      LIMIT 1";
    $prevChapter = "";
    $prevChapLi = "<li class='previous disabled'><a href='#'><small><span aria-hidden='true'>&larr;</span> Previous</small></a></li>";
    if ($prevresult = mysqli_query(dbConnect(), $prevsql)) {
      $prevChapter = mysqli_fetch_array($prevresult);
      if (mysqli_num_rows($prevresult)) {
        $prevChapLi = "<li class='previous'><a href='$prevChapter[location]' id='prevPage' rel='prev'><small><span aria-hidden='true'>&larr;</span> Previous</a></small></li>";
      }
    }
    echo "$prevChapLi $nextChapLi";
  }

  // Get breadcrumbs ///////////////////////////////////////////////////////////
  public function breadcrumbs() {
    if ($this->parent != 0) {
      echo "
        <div class='row'>
          <div class='col-xs-12'>
            <ol class='breadcrumb'>
      ";

      foreach($this->breadcrumbs as $bc) {
        echo $bc;
      }

      echo "
            </ol>
          </div>
        </div>
      ";
    }
  }

  // Get Table of Contents /////////////////////////////////////////////////////
  public function tableOfContents() {
    echo "$this->sidebar
         <!-- <ul> -->
    ";

    if ($this->type == 'story' || $this->type == 'blog') {
      findChildren($this->id, $this->id);
    } else {
      findChildren($this->parent, $this->id);
    }
  }

  // Get tags //////////////////////////////////////////////////////////////////
  public function tags() {
    if (!empty($this->tags)) {
      echo "<i class='glyphicon glyphicon-tag'></i> ";
      $tags = explode(",", $this->tags);
      foreach($tags as $tag) {
        echo "<a href='/search/?tag=$tag' class='label label-default'>$tag</a> ";
      }
    }
  }

  // Set Search Details ////////////////////////////////////////////////////////
  public function setSearch() {
    $this->keyword = "";
    $this->urlkeyword = "?a=a";
    $this->s = "";
    $this->tag = "";
    if (!empty($_GET['s'])) { $this->s = $_GET['s']; $this->keyword = $this->s; $this->urlkeyword = "?s=$this->s"; }
    if (!empty($_GET['tag'])) { $this->tag = $_GET['tag']; $this->keyword = $this->tag; $this->urlkeyword = "?tag=$this->tag"; }
    
    $this->title = "Search";
    $this->parent = 0;
    $this->time = "";
    $this->tags = "";
    $this->location = "/search/";
    $this->banner = config('banner');
    $this->titlePrefix = "";
    $this->booktitle = "";
    $this->description = "Search results for $this->keyword.";
    $this->section = "";
    $this->type = "search";
  }

  // Get search results ////////////////////////////////////////////////////////
  public function searchResults() {

      if (!empty($this->tag)) {
        echo "<h2>Results for &quot;$this->tag&quot; tag</h2>";

        $sql = "SELECT * FROM posts WHERE time IS NOT NULL AND time < CURRENT_TIMESTAMP AND tags LIKE '%$this->tag%' ORDER BY time DESC";
      } else {
        echo "<h2>Results for &quot;$this->s&quot;</h2>";
        $sql = "SELECT * FROM posts WHERE time IS NOT NULL AND time < CURRENT_TIMESTAMP AND (body LIKE '%$this->s%' OR title LIKE '%$this->s%') ORDER BY time DESC";
      }

      if ($result = mysqli_query(dbConnect(), $sql)) {
        while ($row = mysqli_fetch_array($result)) {
          $rowdate = "";
          if ($row['type'] == "chapter" || $row['type'] == "post") {
            $rowdate = date_format(date_create($row['time']), "M. j, Y - g:i A");
          }
          $resultPage = new Page($row['id']);
          echo "
            <div class='panel panel-default'>
              <div class='panel-body'>
                <p class='text-muted pull-right'><small>$rowdate</small></p>
                <h3>" . ucfirst($row['type']) . ": <a href='$row[location]'>$row[title]</a></h3>
                <p>" . substr(strip_tags($resultPage->formattedBody), 0, 200) . " . . .</p>
          ";
          $resultPage->tags();
          echo "
              </div>
            </div>
          ";
        }
      }

  }
}

