<?php include './includes/header.php';?>

<?php
    $questionnaire_id = '';

    if(isset($_GET['questionnaire-id'])) {
        $questionnaire_id = $_GET['questionnaire-id'];
        if($questionnaire_id == '') {
            redirect('questionnaire-result.php');
        }

        $stmt3 = $conn->prepare("SELECT * FROM questionnaires WHERE id=:id");
        $stmt3->execute(['id' => $questionnaire_id]);
        $single_qtionries = $stmt3->fetch();
        $q_ids = $single_qtionries['question_ids'];


        $stmt2 = $conn->prepare("SELECT * FROM questions WHERE id IN ($q_ids) AND (type=:type OR type=:type2)");
        $stmt2->execute([
            'type'      => 1,
            'type2'     => 2
        ]);
        $questions = $stmt2->fetchAll();

    } else {
        $stmt = $conn->prepare("SELECT * FROM questionnaires");
        $stmt->execute();
        $all_qtnries = $stmt->fetchAll();
    }
?>

<main>

    <?php echo isset($_GET['questionnaire-id']) ? "<h1>Questionnaire Results</h1>" : "<h1>Questionnaires Results</h1>"; ?>

    <?php if( $questionnaire_id == '' ) : ?>
    <table class="table2">
        <tbody>
        <?php foreach($all_qtnries as $qtnries) : ?>
            <tr>
                <td><?php echo $qtnries['name'] ?></td>
                <td><a href="questionnaire-result.php?questionnaire-id=<?php echo $qtnries['id'] ?>" class="btn-info">View Result</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    

    <?php foreach ($questions as $question): ?>
    <h3 class="result-q-title mt-3"><?php echo $question['question']; ?></h3>
    <?php
        $answers = get_answers_by_question_id($question['id']);
        $data    = [];
        $labels  = [];
    ?>
    <?php if ($answers > 0): ?>
    <?php foreach ($answers as $answer): ?>
    <?php
        array_push($data, count_global_answers($answer['id']));
        array_push($labels, $answer['answer']);
    ?>
    <?php endforeach;?>
    <?php
        $data = implode(', ', $data);
        $labels = sprintf("'%s'", implode("','", $labels ) );
    ?>
    <?php endif; ?>
    <button class="btn" type="bar" id="change_type_<?php echo $question['id']; ?>">View Pie Chart</button>
    <div class="border-bottom-1 chart_canv_<?php echo $question['id']; ?>"><canvas id="question_<?php echo $question['id']; ?>"></canvas></div>
    <script>
    $(document).ready(function($) {
        loadBarChart( 
            [<?php echo $labels; ?>] , 
            "", 
            [<?php echo $data; ?>] ,
            "question_<?php echo $question['id']; ?>", 
            "<?php echo $question['question']; ?>", 
            $('#change_type_<?php echo $question['id']; ?>').attr('type')
        )
        
        $(document).on('click', '#change_type_<?php echo $question['id']; ?>', function() {
            if($(this).attr('type')  == 'bar') {
                $(this).attr('type', 'pie')
                $(this).text('View Bar Chart')
            } else {
                $(this).attr('type', 'bar')
                $(this).text('View Pie Chart')
            }
            $("#question_<?php echo $question['id']; ?>").remove();
            $(".chart_canv_<?php echo $question['id']; ?>").append('<canvas id="question_<?php echo $question['id']; ?>"></canvas>');

            loadBarChart( 
                [<?php echo $labels; ?>] , 
                "", 
                [<?php echo $data; ?>] ,
                "question_<?php echo $question['id']; ?>", 
                "<?php echo $question['question']; ?>", 
                $('#change_type_<?php echo $question['id']; ?>').attr('type'),
                true
            )
        })
    });
    </script>
    <?php endforeach; ?>
    <?php endif; ?>

</main>

<?php include './includes/footer.php';?>