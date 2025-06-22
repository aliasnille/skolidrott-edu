// Loading animation
setTimeout(() => {
    const loader = document.getElementById('loader');
    const content = document.getElementById('content');
    loader.style.display = 'none';
    content.style.display = 'block';
    }, 3000
);

/*
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    loader.style.display = 'none';
});
*/

// Duplicate course modal
const duplicateCourseModal = document.getElementById('duplicateCourseModal')
if (duplicateCourseModal) {
    duplicateCourseModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const courseID = button.getAttribute('data-bs-course-id')
        const courseTitle = button.getAttribute('data-bs-course-title')
        const modalCourseIDInput = duplicateCourseModal.querySelector('.modal-body input[name="course_id"]')
        const modalCourseTitleInput = duplicateCourseModal.querySelector('.modal-body input[name="course_title"]')

        modalCourseIDInput.value = courseID
        modalCourseTitleInput.value = courseTitle
    })
}