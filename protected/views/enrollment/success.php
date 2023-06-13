<?php
/* @var $this EnrollmentController */
/* @var $enrollment Enrollment */
/* @var $course Course */

$this->breadcrumbs = array(
    'Fazer inscrição em cursos' => array('/enrollment'),
    $course->course_name,
);

?>
<h1>Inscrição feita com sucesso</h1>

<p>
    <?php echo $enrollment->enr_firstname ?>, sua inscrição no curso
    '<?php echo $course->course_name ?>' foi concluída com <b>sucesso!</b> 
    Em breve entraremos em contato através de seu email
    <b><?php echo $enrollment->enr_email; ?></b> com mais informações.
</p>
