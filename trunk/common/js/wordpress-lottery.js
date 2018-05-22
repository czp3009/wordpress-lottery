const action = 'wordpress_lottery_doLottery';
//viewData
// noinspection JSUnresolvedVariable
const ajaxUrl = wordpressLotteryViewData.ajaxUrl;
// noinspection JSUnresolvedVariable
const postId = wordpressLotteryViewData.postId;

document.querySelectorAll('.wordpress-lottery-container').forEach(container => {
    const input = container.getElementsByClassName('wordpress-lottery-input')[0];
    const button = container.getElementsByClassName('wordpress-lottery-button')[0];
    const loader = container.getElementsByClassName('wordpress-lottery-loader')[0];
    const canvas = container.getElementsByClassName('wordpress-lottery-canvas')[0];

    // noinspection JSUndefinedPropertyAssignment
    button.onclick = () => {
        canvas.innerHTML = '';
        loader.hidden = false;

        let urlSearchParams = new URLSearchParams();
        urlSearchParams.append('action', action);
        urlSearchParams.append('postId', postId);
        urlSearchParams.append('winnerCount', input.value);

        //admin-ajax 仅支持 formData
        fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: urlSearchParams
            }
        )
            .then(response => {
                if (!response.ok) {
                    if (response.status === 400) {
                        throw Error('登陆后才能检测血统')
                    } else {
                        throw Error('未知错误: ' + response.statusText)
                    }
                }
                return response
            })
            .then(response => response.json())
            .then(response => {
                if (!response.success) {
                    throw Error(response.data.message)
                }
                response.data.forEach(comment => {
                    let node = document.createElement('p');
                    // noinspection JSUnresolvedVariable
                    let textNode = document.createTextNode(`评论 ID: ${comment.commentId}, 用户名: ${comment.commentAuthor}, 电子邮件: ${comment.commentAuthorEmail}`);
                    node.appendChild(textNode);
                    canvas.appendChild(node);
                });
            })
            .catch((error) => {
                canvas.innerHTML = error.message
            })
            .finally(() => {
                loader.hidden = true
            })
    }
});
