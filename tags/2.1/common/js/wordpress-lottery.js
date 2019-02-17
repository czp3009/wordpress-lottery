document.querySelectorAll('.wordpress-lottery-container').forEach(container => {
    const input = container.getElementsByClassName('wordpress-lottery-input')[0];
    const button = container.getElementsByClassName('wordpress-lottery-button')[0];
    const loader = container.getElementsByClassName('wordpress-lottery-loader')[0];
    const canvas = container.getElementsByClassName('wordpress-lottery-canvas')[0];

    button.onclick = () => {
        canvas.innerHTML = '';
        loader.hidden = false;

        //旧版本 edge 不支持 URLSearchParams, 此问题无法解决
        let urlSearchParams = new URLSearchParams();
        urlSearchParams.append('action', 'wordpress_lottery_doLottery');
        // noinspection JSUnresolvedVariable
        urlSearchParams.append('postId', wordpressLotteryViewData.postId);
        urlSearchParams.append('winnerCount', input.value);

        //admin-ajax 仅支持 formData
        // noinspection JSUnresolvedVariable
        fetch(wordpressLotteryViewData.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: urlSearchParams
            }
        )
            .then(response => {
                if (!response.ok) {
                    if (response.status === 400) {
                        throw new Error('登陆后才能检测血统')
                    } else {
                        throw new Error('未知错误: ' + response.statusText)
                    }
                }
                return response
            })
            .then(response => response.json())
            .then(response => {
                if (!response.success) {
                    throw new Error(response.data.message)
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
            .finally(() => {    //edge 不支持 finally, 此问题无法解决
                loader.hidden = true
            })
    }
});
