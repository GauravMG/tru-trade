var accountType = null

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

        const accountType = document.querySelector('input[name="accountType"]:checked')?.value
        if ([undefined, null].includes(accountType)) {
            toastr.error("Please select account type")
            return
        }

        let server = []
        switch (accountType) {
            case "funded": const selectedServersRadio = document.querySelector('input[name="server"]:checked')?.value
                if (selectedServersRadio) {
                    server = [selectedServersRadio]
                }

                break

            case "brokered":
                const selectedServers = document.querySelectorAll('input[name="server"]:checked')
                server = Array.from(selectedServers).map(checkbox => checkbox.value)
        }
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
                accountType: accountType,
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
})

function onChangeAccountType(value, selectedServers = []) {
    let serverDiv = document.getElementById("server-div")

    switch (value) {
        case "funded":
            serverDiv.innerHTML = `
                <label>Server</label>
                <span style="font-size: 13px;"> (please select 1)</span>
                <div class="form-check">
                    <label class="form-check-label"><input class="form-check-input" type="radio" name="server" id="serverA" value="a"> A</label>
                </div>
                <div class="form-check">
                    <label class="form-check-label"><input class="form-check-input" type="radio" name="server" id="serverB" value="b"> B</label>
                </div>
            `

            if (selectedServers?.length) {
                if (selectedServers[0] === 'a') {
                    document.getElementById('serverA').checked = true
                } else if (selectedServers[0] === 'b') {
                    document.getElementById('serverB').checked = true
                }
            }

            document.getElementById("newServerAccountSizeContainer").style.display = "block"

            break

        case "brokered":
            serverDiv.innerHTML = `
                <label>Server</label>
                <div class="form-check">
                    <label class="form-check-label"><input class="form-check-input" type="checkbox" name="server" id="serverA" value="a"> A</label>
                </div>
                <div class="form-check">
                    <label class="form-check-label"><input class="form-check-input" type="checkbox" name="server" id="serverB" value="b"> B</label>
                </div>
            `

            if (selectedServers?.length) {
                document.querySelectorAll('input[name="server"]').forEach((checkbox) => {
                    if (selectedServers.includes(checkbox.value)) {
                        checkbox.checked = true
                    }
                })
            }

            document.getElementById("newServerAccountSizeContainer").style.display = "none"

            break
    }
}

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

            accountType = leadDetails.accountType ?? null
            if (accountType) {
                if (accountType === 'funded') {
                    document.getElementById('accountTypeFunded').checked = true
                } else if (accountType === 'brokered') {
                    document.getElementById('accountTypeBrokered').checked = true
                }

                let selectedServers = leadDetails.server ?? []
                if (selectedServers?.length) {
                    selectedServers = typeof selectedServers === "string" ? JSON.parse(selectedServers) : selectedServers
                }

                onChangeAccountType(accountType, selectedServers)
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

            if ((accountType ?? "").trim() === "" || (accountType === "funded" && response.total >= 20) || (accountType === "brokered" && response.total >= 8)) {
                document.getElementById("addAccountButtonSource").style.display = "none"
            } else {
                document.getElementById("addAccountButtonSource").style.display = "block"
            }

            let accountList = document.getElementById("account-list");

            accountList.innerHTML = "";

            for (let server of response.data) {
                accountList.innerHTML += `
                    <tr>
                        <td>${server.systemType}</td>
                        <td>${server.accountNumber}</td>
                        <td>${addKInAmount(server.accountSize)}</td>
                        <td>$ ${server.accountCost}</td>
                        <td>${server.multiplier}x</td>
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
    const systemType = document.getElementById("newAccountSystemType").value
    if ((systemType ?? "").trim() === "") {
        toastr.error("Please enter system type")
        return
    }

    const accountNumber = document.getElementById("newAccountNumber").value
    if ((accountNumber ?? "").trim() === "") {
        toastr.error("Please enter account number")
        return
    }

    let accountSize = null
    const accountType = document.querySelector('input[name="accountType"]:checked')?.value
    if (accountType === "funded") {
        accountSize = document.getElementById("newAccountSize").value
        if ((accountSize ?? "").trim() === "") {
            toastr.error("Please enter account size")
            return
        }
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

    $.ajax({
        url: `/lead/${leadId}/create-account`,
        method: 'POST',
        data: {
            systemType,
            accountNumber,
            accountSize,
            accountCost,
            multiplier
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
                        <td>${(payment.paymentStatus ?? "") === "paid" ? `<span><i class="fa fa-check"></i> ${formatDateWithoutTime(payment.paymentMadeOn)}</span>` : `<button class="btn btn-primary" onclick="markPaymentAsPaid(${payment.paymentId})">Pay</button>`}</td>
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

function markPaymentAsPaid(paymentId) {
    $("#paymentDetailsPaymentId").val(paymentId)
    $('#modal-payment-pay').modal('show')
}

function savePaymentDetails() {
    const paymentId = document.getElementById("paymentDetailsPaymentId").value
    if ((paymentId ?? "").trim() === "") {
        toastr.error("Invalid payment details")
        return
    }

    const paymentMadeOn = document.getElementById("paymentDetailsPaymentModeOn").value
    if ((paymentMadeOn ?? "").trim() === "") {
        toastr.error("Please select when payment was made")
        return
    }

    $.ajax({
        url: `/lead/${leadId}/update-payments/${paymentId}`,
        method: 'POST',
        data: {
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
