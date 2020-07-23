<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <title>Load File</title>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-5">
            <form action="/" method="post" enctype="multipart/form-data">
                <input type="file" name="file">

                <button class="btn btn-success">Load</button>
            </form>
        </div>
    </div>

    <?php if(isset($statistic_data) && !empty($statistic_data)) :?>
        <div class="row mt-4">
            <div class="col">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Customer Id</th>
                        <th scope="col">Number of calls within the same continent</th>
                        <th scope="col">Total Duration of calls within the same continent</th>
                        <th scope="col">Total number of all calls</th>
                        <th scope="col">The total duration of all calls</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($statistic_data as $row) : ?>
                        <tr>
                            <th scope="row"><?php echo $row['customer_id']; ?></th>
                            <td><?php echo $row['count_calls_within_same_continent']; ?></td>
                            <td><?php echo $row['duration_calls_within_same_continent']; ?></td>
                            <td><?php echo $row['total_count_calls']; ?></td>
                            <td><?php echo $row['total_duration']; ?></td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>


</body>
</html>