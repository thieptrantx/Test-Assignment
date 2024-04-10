<h1>MySQL</h1>
<hr />
<p>Table tbl_contents have 1M records with columns: id, cate_id, title, content, published(0,1), created_at, updated_at (model Content)</p>
<p>Table tbl_categories have 1M records with columns: id, name, published(0,1), created_at, updated_at (model Category)</p>
<ul>
	<li>1. Write a mysql to find all the contents (title or content) matching the keyword "Computer on Module" are published (1), order by updated_at, limit 100 records.</li>
	<li>2. Write a mysql to find all the contents  matching the category "CPU" are published (1), order by updated_at, limit 100 records.</li>
	<li>3. Please explain which fields in database should be indexed.</li>
</ul>
<!-- Write your answer below -->
<?php

//code...