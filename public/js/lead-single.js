$(document).ready(function () {
    fetchLeadDetails("#lead-details")

    // Handle tab switching and show the loader for the active tab
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        // Get the target tab ID
        var targetTabId = $(e.target).attr('href')  // This will give #lead-details, #manage-accounts, etc.

        // Hide all loaders
        $('.overlay').show()

        if (targetTabId.replace("#", "") === "lead-details") {
            fetchLeadDetails(targetTabId)
        } else if (targetTabId.replace("#", "") === "manage-accounts") {
            fetchAccounts(targetTabId)
        } else if (targetTabId.replace("#", "") === "manage-payments") {
            fetchPayments(targetTabId)
        } else if (targetTabId.replace("#", "") === "manage-documents") {
            fetchDocuments(targetTabId)
        }
    })

    // $('#paymentDetailsPaymentModeOn').datepicker({
    //     format: "MM yyyy", // Format to show "October 2024"
    //     startView: "months", // Start the picker at the months view
    //     minViewMode: "months", // Restrict to selecting only months
    //     autoclose: true // Close the picker after selection
    // })

    $("#formLeadDetailsSubmit").on("submit", function (e) {
        e.preventDefault()

        const selectedServers = document.querySelectorAll('input[name="server"]:checked')
        const server = Array.from(selectedServers).map(checkbox => checkbox.value)
        if (!server?.length) {
            toastr.error("Please select servers")
            return
        }

        const serverCost = document.getElementById("serverCost").value
        if ((serverCost ?? "").trim() === "") {
            toastr.error("Please enter overall server cost")
            return
        }

        $.ajax({
            url: `/lead/${leadId}/update-details`,
            method: 'POST',
            data: {
                server: JSON.stringify(server), // Convert array to JSON string
                serverCost: serverCost
            },
            beforeSend: function () {
                $('#lead-details-loader').fadeIn()
            },
            complete: function () {
                $('#lead-details-loader').fadeOut()
                fetchLeadDetails("#lead-details")
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message)
                } else {
                    toastr.error(response.message)
                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', error)
                toastr.error("An error occurred.")
            }
        })
    })

    $('#document').on('change', function () {
        const fileName = $(this).val().split('\\').pop(); // Get the file name
        $(this).next('.custom-file-label').html(fileName); // Set the file name in the label
    });

    // Monitor changes to the Quickfund Account radio buttons
    $('input[name="newAccountIsQuickfundAccount"]').on('change', function () {
        const isQuickfundAccount = $(this).val() === 'yes';

        // Toggle visibility of Quickfund Cost field based on selection
        $('#newAccountQuickfundCostContainer').toggle(isQuickfundAccount);

        // If "Yes" is selected, make Quickfund Cost mandatory
        if (isQuickfundAccount) {
            $('#newAccountQuickfundCost').attr('required', true);
        } else {
            // If "No" is selected, hide field, clear value, and remove mandatory attribute
            $('#newAccountQuickfundCost').val('').removeAttr('required');
        }
    });
})

function fetchLeadDetails(targetTabId) {
    $.ajax({
        url: `/lead/${leadId}/fetch-details`,
        method: "GET",
        data: {},
        beforeSend: function () { },
        complete: function () {
            $(targetTabId + '-loader').fadeOut()
        },
        success: function (response) {
            if (!response.success) {
                toastr.error(response.message)
                return
            }

            const { leadDetails } = response.data

            $("#name").val(leadDetails.name)
            $("#leadCreatedAt").html(`Created On - ${formatDate(leadDetails.createdAt)}`)
            $("#status").val(capitalizeFirstLetter(leadDetails.status ?? ""))
            $("#source").val(leadDetails.source ?? "")

            let selectedServers = leadDetails.server ?? []
            if (selectedServers?.length) {
                selectedServers = typeof selectedServers === "string" ? JSON.parse(selectedServers) : selectedServers

                let accountServerDiv = document.getElementById("account-server-div")

                if (selectedServers.length === 1 && selectedServers[0] === "a") {
                    accountServerDiv.innerHTML = `
                    <label>Server <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <label class="form-check-label"><input class="form-check-input" type="radio" name="newAccountServerType" id="newAccountServerTypeA" value="a"> A</label>
                    </div>
                    `
                } else if (selectedServers.length === 1 && selectedServers[0] === "b") {
                    accountServerDiv.innerHTML = `
                    <label>Server <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <label class="form-check-label"><input class="form-check-input" type="radio" name="newAccountServerType" id="newAccountServerTypeB" value="b"> B</label>
                    </div>
                    `
                } else if (selectedServers.length === 2) {
                    accountServerDiv.innerHTML = `
                    <label>Server <span class="text-danger">*</span></label><span style="font-size: 13px;"> (please select 1)</span>
                    <div class="form-check">
                        <label class="form-check-label"><input class="form-check-input" type="radio" name="newAccountServerType" id="newAccountServerTypeA" value="a"> A</label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label"><input class="form-check-input" type="radio" name="newAccountServerType" id="newAccountServerTypeB" value="b"> B</label>
                    </div>
                    `
                }
            }

            if (selectedServers?.length) {
                document.querySelectorAll('input[name="server"]').forEach((checkbox) => {
                    if (selectedServers.includes(checkbox.value)) {
                        checkbox.checked = true
                    }
                })
            }

            $("#serverCost").val(leadDetails.serverCost ?? null)

            $("#contactNameExternal").val(leadDetails.contactNameExternal ?? "")
            $("#contactCompanyNameExternal").val(leadDetails.contactCompanyNameExternal ?? "")
            $("#contactEmailIdExternal").val(leadDetails.contactEmailIdExternal ?? "")
            $("#contactPhoneExternal").val(leadDetails.contactPhoneExternal ?? "")
            $("#contactTagsExternal").val(JSON.parse((leadDetails.contactTagsExternal ?? "[]")).join(", "))

            const radioButtonsAccountType = document.querySelectorAll('input[name="accountType"]')
            radioButtonsAccountType.forEach((radio) => {
                radio.addEventListener('change', function () {
                    onChangeAccountType(this.value)
                });
            });
        },
        error: function (error) {
            console.log(`error`, error)
            toastr.error("An error occurred.")
        },
    })
}

function initializeDTAccountList() {
    $("#dtAccountList").DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    })
}

function fetchAccounts(targetTabId) {
    $.ajax({
        url: `/lead/${leadId}/fetch-accounts`,
        method: "GET",
        data: {},
        beforeSend: function () {
            // Destroy the existing DataTable before reinitializing
            if ($.fn.DataTable.isDataTable("#dtAccountList")) {
                $('#dtAccountList').DataTable().destroy()
            }
        },
        complete: function () {
            $(targetTabId + '-loader').fadeOut()
        },
        success: function (response) {
            if (!response.success) {
                toastr.error(response.message)
                return
            }

            let accountList = document.getElementById("account-list");

            accountList.innerHTML = "";

            for (let server of response.data) {
                accountList.innerHTML += `
                    <tr>
                        <td>${(server.serverType ?? "").toUpperCase()}</td>
                        <td>${(server.accountType ?? "").toUpperCase()}</td>
                        <td>${server.accountNumber}</td>
                        <td>$${server.accountCost}</td>
                        <td>${server.multiplier}x</td>
                        <td>${(server.isQuickfundAccount ?? "").toUpperCase()}</td>
                        <td>${server.quickfundCost ? `$${server.quickfundCost}` : ""}</td>
                        <td>${formatDate(server.createdAt)}</td>
                    </tr>
                `
            }

            // Reinitialize the DataTable after the data is loaded
            initializeDTAccountList()
        },
        error: function (error) {
            console.log(`error`, error)
            toastr.error("An error occurred.")
        },
    })
}

function saveAccount() {
    const serverType = document.querySelector('input[name="newAccountServerType"]:checked')?.value
    if ((serverType ?? "").trim() === "") {
        toastr.error("Please select server type")
        return
    }

    const accountType = document.querySelector('input[name="newAccountType"]:checked')?.value
    if ((accountType ?? "").trim() === "") {
        toastr.error("Please select account type")
        return
    }

    const accountNumber = document.getElementById("newAccountNumber").value
    if ((accountNumber ?? "").trim() === "") {
        toastr.error("Please enter account number")
        return
    }

    const accountCost = document.getElementById("newAccountCost").value
    if ((accountCost ?? "").trim() === "") {
        toastr.error("Please enter account cost")
        return
    }

    const multiplier = document.getElementById("newAccountMultiplier").value
    if ((multiplier ?? "").trim() === "") {
        toastr.error("Please enter multiplier")
        return
    }

    const isQuickfundAccount = document.querySelector('input[name="newAccountIsQuickfundAccount"]:checked')?.value
    if ((isQuickfundAccount ?? "").trim() === "") {
        toastr.error("Please select if account is quickfund or not")
        return
    }

    let quickfundCost = null
    if (isQuickfundAccount === "yes") {
        quickfundCost = document.getElementById("newAccountQuickfundCost").value
        if ((quickfundCost ?? "").trim() === "") {
            toastr.error("Please enter quickfund cost")
            return
        }
    }

    $.ajax({
        url: `/lead/${leadId}/create-account`,
        method: 'POST',
        data: {
            serverType,
            accountType,
            accountNumber,
            accountCost,
            multiplier,
            isQuickfundAccount,
            quickfundCost
        },
        beforeSend: function () {
            $('#manage-accounts-loader').fadeIn()
        },
        complete: function () {
            $('#manage-accounts-loader').fadeOut()
            fetchAccounts("#manage-accounts")
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message)
                $('#modal-add-account').modal('hide')
                document.getElementById('formNewAccount').reset()
            } else {
                toastr.error(response.message)
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', error)
            toastr.error("An error occurred.")
        }
    })
}

function initializeDTPaymentList() {
    $("#dtPaymentList").DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    })
}

function fetchPayments(targetTabId) {
    $.ajax({
        url: `/lead/${leadId}/fetch-payments`,
        method: "GET",
        data: {},
        beforeSend: function () {
            // Destroy the existing DataTable before reinitializing
            if ($.fn.DataTable.isDataTable("#dtPaymentList")) {
                $('#dtPaymentList').DataTable().destroy()
            }
        },
        complete: function () {
            $(targetTabId + '-loader').fadeOut()
        },
        success: function (response) {
            if (!response.success) {
                toastr.error(response.message)
                return
            }

            let paymentList = document.getElementById("payment-list");

            paymentList.innerHTML = "";

            for (let payment of response.data) {
                paymentList.innerHTML += `
                    <tr>
                        <td>${payment.month}</td>
                        <td>$ ${payment.amount}</td>
                        <td>${capitalizeFirstLetter(payment.paymentStatus ?? "")}</td>
                        <td>${(payment.paymentStatus ?? "") === "paid" ? `<span><i class="fa fa-check"></i> ${formatDateWithoutTime(payment.paymentMadeOn)}</span>` : `<button class="btn btn-primary" onclick="markPaymentAsPaid('${payment.paymentId}', '${payment.month}', '${payment.amount}')">Pay</button>`}</td>
                    </tr>
                `
            }

            // Reinitialize the DataTable after the data is loaded
            initializeDTPaymentList()
        },
        error: function (error) {
            console.log(`error`, error)
            toastr.error("An error occurred.")
        },
    })
}

function markPaymentAsPaid(paymentId, month, amount) {
    $("#paymentDetailsPaymentId").val(paymentId)
    $("#paymentDetailsMonth").val(month)
    $("#paymentDetailsAmount").val(amount)
    $('#modal-payment-pay').modal('show')
}

function savePaymentDetails() {
    const paymentId = document.getElementById("paymentDetailsPaymentId").value
    const month = document.getElementById("paymentDetailsMonth").value
    const amount = document.getElementById("paymentDetailsAmount").value
    // if ((paymentId ?? "").trim() === "") {
    //     toastr.error("Invalid payment details")
    //     return
    // }

    const paymentMadeOn = document.getElementById("paymentDetailsPaymentModeOn").value
    if ((paymentMadeOn ?? "").trim() === "") {
        toastr.error("Please select when payment was made")
        return
    }

    $.ajax({
        url: `/lead/${leadId}/update-payments`,
        method: 'POST',
        data: {
            paymentId,
            month,
            amount,
            paymentMadeOn
        },
        beforeSend: function () {
            $('#manage-payments-loader').fadeIn()
        },
        complete: function () {
            $('#manage-payments-loader').fadeOut()
            fetchPayments("#manage-payments")
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message)
                $('#modal-payment-pay').modal('hide')
                document.getElementById('formPaymentDetails').reset()
            } else {
                toastr.error(response.message)
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', error)
            toastr.error("An error occurred.")
        }
    })
}

function displayFile(filePath, divId) {
    const extension = filePath.split('.').pop().toLowerCase();

    if (extension === 'pdf') {
        // Display PDF
        $(`#${divId}`).show().html('<iframe src="' + filePath + '" width="100%" height="100%"></iframe>');
    } else if (extension === 'docx') {
        // Display DOCX
        loadDocx(filePath);
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
        // Display Image
        $(`#${divId}`).show().html('<img src="' + filePath + '" width="100%" height="100%" />');
    } else {
        alert('Unsupported file type.');
    }
}

function loadDocx(filePath) {
    // Fetch the DOCX file and convert it to HTML
    fetch(filePath)
        .then(response => response.blob())
        .then(blob => {
            const reader = new FileReader();
            reader.onload = function (event) {
                const zip = new JSZip();
                zip.loadAsync(event.target.result).then(function (content) {
                    // Extract text from DOCX file
                    const docText = content.files['word/document.xml'].async('text');
                    docText.then(function (text) {
                        $('#viewerContract').show().html('<div>' + text + '</div>'); // Display the text
                    });
                });
            };
            reader.readAsArrayBuffer(blob);
        })
        .catch(err => {
            alert('Error loading DOCX file.');
        });
}

function fetchDocuments(targetTabId) {
    $.ajax({
        url: `/lead/${leadId}/fetch-documents`,
        method: "GET",
        data: {},
        beforeSend: function () { },
        complete: function () {
            $(targetTabId + '-loader').fadeOut()
        },
        success: function (response) {
            if (!response.success) {
                toastr.error(response.message)
                return
            }

            const documentViewerContainer = document.getElementById("manage-documents-content")
            
            while (documentViewerContainer.children.length > 1) {
                documentViewerContainer.removeChild(documentViewerContainer.firstElementChild)
            }

            for (let item of response.data) {
                const viewerDivId = `viewerDocument_${item.documentId}`

                const html = `
                    <div class="card document-card">
                        <div class="card-header">
                            <h3 class="card-title">${capitalizeFirstLetter(item.documentType)}</h3>
                            <div>
                                <a target="_blank" href="${item.documentLink}"><i class="fa fa-eye view-icon"></i></a>
                                <a onclick="onClickDownloadFile('${item.documentLink}')"><i class="fa fa-download view-icon"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="${viewerDivId}" class="viewer-document"></div>
                        </div>
                    </div>
                `
                // Create a temporary container to hold the HTML string as a DOM element
                const tempContainer = document.createElement('div');
                tempContainer.innerHTML = html;

                // Get the element to prepend
                const newElement = tempContainer.firstElementChild;

                // Find the last child of the parent div
                const lastElement = documentViewerContainer.lastElementChild;

                // Insert the new element before the last child
                if (lastElement) {
                    documentViewerContainer.insertBefore(newElement, lastElement);
                } else {
                    // If there is no last element, just append it as the first child
                    documentViewerContainer.appendChild(newElement);
                }
                
                displayFile(item.documentLink, viewerDivId)
            }

        },
        error: function (error) {
            console.log(`error`, error)
            toastr.error("An error occurred.")
        },
    })
}

function uploadDocument() {
    const documentType = document.getElementById('documentType').value
    const fileInput = document.getElementById('document')

    if ((documentType ?? "").trim() === "") {
        toastr.error("Please select document type")
        return
    }

    if (fileInput.files.length === 0) {
        toastr.error("Please select a file")
        return
    }

    const file = fileInput.files[0];
    const allowedTypes = [
        'application/pdf',
        // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    // Validate file type
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please upload a PDF, or image file (JPEG, PNG, GIF).');
        return;
    }

    let formData = new FormData()

    formData.append('documentType', documentType)
    formData.append('document', fileInput.files[0])

    $.ajax({
        url: `/lead/${leadId}/upload-document`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#manage-documents-loader').fadeIn()
        },
        complete: function () {
            $('#manage-documents-loader').fadeOut()
            fetchDocuments("#manage-documents")
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message)

                document.getElementById('documentType').value = "-"
                document.getElementById('document').value = ""
                $("#document").next('.custom-file-label').html("Choose file"); // Set the file name in the label
            } else {
                toastr.error(response.message)
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', error)
            toastr.error("An error occurred.")
        }
    })
}
