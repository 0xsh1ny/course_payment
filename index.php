<?php
require 'vendor/autoload.php';
require 'config.php';
$result = $db->query("SELECT * FROM courses");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Platform</title>
</head>
<body>
    <h1>Available Courses</h1>
    <?php while ($course = $result->fetch_assoc()): ?>
        <div>
            <h2><?php echo $course['name']; ?></h2>
            <p><?php echo $course['description']; ?></p>
            <p>Price: $<?php echo $course['price'] / 100; ?></p>
            <form action="checkout.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>
