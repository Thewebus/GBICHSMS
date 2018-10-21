<?php
$data = $_POST['sms'];
if(strlen($data) > 160)
{
    $data = substr($data, 0, 160);
    $data = $data . '...';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Previsualisation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h2>Previsualiation</h2>
    <div>Information</div>
    <form >
        <div class="form-group row">
            <div class="col-sm-3">
                <textarea class="form-control " readonly rows="5"><?= $data; ?></textarea>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-2">
                <button class="btn btn-danger" id="btnRetour">Retour</button>
            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-success">Valider</button>
            </div>


        </div>
        <div>sms à envoyer le <?= $_POST['date_diff']; ?> à <?= $_POST['heure_diff']; ?></div>

    </form>
</div>
</body>
</html>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $('#btnRetour').click(function (event) {
        event.preventDefault();
        parent.history.back();
    });

</script>